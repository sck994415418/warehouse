<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;

/**
 * 根据附件表的id返回url地址
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function geturl($id)
{
	if ($id) {
		$geturl = Db::name("attachment")->where(['id' => $id])->find();
		if($geturl['status'] == 1) {
			//审核通过
			return $geturl['filepath'];
		} elseif($geturl['status'] == 0) {
			//待审核
			return '/uploads/xitong/beiyong1.jpg';
		} else {
			//不通过
			return '/uploads/xitong/beiyong2.jpg';
		} 
    }
    return false;
}


/**
 * [SendMail 邮件发送]
 * @param [type] $address  [description]
 * @param [type] $title    [description]
 * @param [type] $message  [description]
 * @param [type] $from     [description]
 * @param [type] $fromname [description]
 * @param [type] $smtp     [description]
 * @param [type] $username [description]
 * @param [type] $password [description]
 */
function SendMail($address)
{
    vendor('phpmailer.PHPMailerAutoload');
    //vendor('PHPMailer.class#PHPMailer');
    $mail = new PHPMailer();
     // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();                
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet='UTF-8';         
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address); 

    $data = Db::name('emailconfig')->where('email','email')->find();
            $title = $data['title'];
            $message = $data['content'];
            $from = $data['from_email'];
            $fromname = $data['from_name'];
            $smtp = $data['smtp'];
            $username = $data['username'];
            $password = $data['password'];   
    // 设置邮件正文
    $mail->Body=$message;           
    // 设置邮件头的From字段。
    $mail->From=$from;  
    // 设置发件人名字
    $mail->FromName=$fromname;  
    // 设置邮件标题
    $mail->Subject=$title;          
    // 设置SMTP服务器。
    $mail->Host=$smtp;
    // 设置为"需要验证" ThinkPHP 的config方法读取配置文件
    $mail->SMTPAuth=true;
    //设置html发送格式
    $mail->isHTML(true);           
    // 设置用户名和密码。
    $mail->Username=$username;
    $mail->Password=$password; 
    // 发送邮件。
    return($mail->Send());
}


/**
 * 阿里大鱼短信发送
 * @param [type] $appkey    [description]
 * @param [type] $secretKey [description]
 * @param [type] $type      [description]
 * @param [type] $name      [description]
 * @param [type] $param     [description]
 * @param [type] $phone     [description]
 * @param [type] $code      [description]
 * @param [type] $data      [description]
 */
function SendSms($param,$phone)
{
    // 配置信息
    import('dayu.top.TopClient');
    import('dayu.top.TopLogger');
    import('dayu.top.request.AlibabaAliqinFcSmsNumSendRequest');
    import('dayu.top.ResultSet');
    import('dayu.top.RequestCheckUtil');

    //获取短信配置
    $data = Db::name('smsconfig')->where('sms','sms')->find();
            $appkey = $data['appkey'];
            $secretkey = $data['secretkey'];
            $type = $data['type'];
            $name = $data['name'];
            $code = $data['code'];
    
    $c = new TopClient();
    $c ->appkey = $appkey;
    $c ->secretKey = $secretkey;
    
    $req = new AlibabaAliqinFcSmsNumSendRequest();
    //公共回传参数，在“消息返回”中会透传回该参数。非必须
    $req ->setExtend("");
    //短信类型，传入值请填写normal
    $req ->setSmsType($type);
    //短信签名，传入的短信签名必须是在阿里大于“管理中心-验证码/短信通知/推广短信-配置短信签名”中的可用签名。
    $req ->setSmsFreeSignName($name);
    //短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开。
    $req ->setSmsParam($param);
    //短信接收号码。支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码。
    $req ->setRecNum($phone);
    //短信模板ID，传入的模板必须是在阿里大于“管理中心-短信模板管理”中的可用模板。
    $req ->setSmsTemplateCode($code);
    //发送
    

    $resp = $c ->execute($req);
}


/**
 * 替换手机号码中间四位数字
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function hide_phone($str){
    $resstr = substr_replace($str,'****',3,4);  
    return $resstr;  
}
function address_fun($data = array()){
    $address1 = db('address')->where('status',1)->column("id2 as id,name2 as title,id1 as p_id,field");
    $address2 = db('address')->where('status',1)->column("id3 as id,name3 as title,id2 as p_id,field");
//    dump($data);die;
//    $data = array_splice($data,0,3);
    $address3 = db('address')
        ->where(['status'=>1])
        ->where('id4','IN',$data)
        ->column("id4 as id,name4 as title,id3 as p_id,field,true as checked");
    $address4 = db('address')
        ->where('status',1)
        ->where('id4','NOTIN',$data)
        ->column("id4 as id,name4 as title,id3 as p_id,field");
//    dump($address4);
    $address3 = array_merge($address3,$address4);
//    if(!empty($address3) and !empty($data)){
//        foreach ($address3 as $k=>$v){
//            if(in_array($k,$data))
//            {
//                $address3[$k]['checked'] = true;
//            }
//        }
//    }
//    $address4 = db('address')->column("id5,name5,id4 as p_id");

    $arr = array_merge($address1,$address2);
    $arr2 = array_merge($arr,$address3);

    $res = getTree($arr2);
//    $res = array_splice($res,0,4);
//    dump($res);die;
    return $res;

}
function getTree($list, $pk='id', $pid = 'p_id', $child = 'children', $root = 1) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
function address_funsss($data=array()){

    $address = db('address')
        ->where(['id1'=>1,'status'=>1])
        ->column('id2,name2,id3');
    $address = array_merge($address);
    if(!empty($address)){
        $address = array_map(function($address) {
            return array(
                'id' => $address['id2'],
                'title' => $address['name2'],
                'field' => 'address_ids[]'
            );
        }, $address);
        foreach ($address as $k=>$v){
            $address[$k]['children'] = db('address')
                ->where('id2',$address[$k]['id'])
                ->column('id3,name3,id4');
            $address[$k]['children'] = array_merge($address[$k]['children']);
            if(!empty($address[$k]['children'])){
                foreach ($address[$k]['children'] as $ks=>$vs){
                    $address[$k]['children'][$ks]['id'] = $address[$k]['children'][$ks]['id3'];
                    $address[$k]['children'][$ks]['title'] = $address[$k]['children'][$ks]['name3'];
                    $address[$k]['children'][$ks]['field'] = 'address_ids[]';
                    unset($address[$k]['children'][$ks]['id3']);
                    unset($address[$k]['children'][$ks]['name3']);
                    unset($address[$k]['children'][$ks]['id4']);
                    $address[$k]['children'][$ks]['children'] = db('address')
                        ->where('id3',$address[$k]['children'][$ks]['id'])
                        ->column('id4,name4,id5');
                    $address[$k]['children'][$ks]['children'] = array_merge($address[$k]['children'][$ks]['children']);
                    if(!empty($address[$k]['children'][$ks]['children'])){
                        foreach ($address[$k]['children'][$ks]['children'] as $kss=>$vss){
                            $address[$k]['children'][$ks]['children'][$kss]['id'] = $address[$k]['children'][$ks]['children'][$kss]['id4'];
                            $address[$k]['children'][$ks]['children'][$kss]['title'] = $address[$k]['children'][$ks]['children'][$kss]['name4'];
                            $address[$k]['children'][$ks]['children'][$kss]['field'] = 'address_ids[]';
                            unset($address[$k]['children'][$ks]['children'][$kss]['id4']);
                            unset($address[$k]['children'][$ks]['children'][$kss]['name4']);
                            unset($address[$k]['children'][$ks]['children'][$kss]['id5']);
                            if(in_array($address[$k]['children'][$ks]['children'][$kss]['id'],$data)){
                                $address[$k]['children'][$ks]['children'][$kss]['checked'] = true;
                            }
                        }
                    }
                }
            }
        }
    }
    return $address;
}

function address_funss($data=array())
{
    $address = db('address')
//        ->field('id2 as id, name2 as title')
        ->where(['id1'=>1,'status'=>1])->select();
    if(!empty($address)) {
        $address_new = [];
        $address_two_new = [];
        $address_three_new = [];
        foreach ($address as $k=>$v){
            $address_new[$address[$k]['id2']]['id'] = $address[$k]['id2'];
            $address_new[$address[$k]['id2']]['title'] = $address[$k]['name2'];
            $address_new[$address[$k]['id2']]['field'] = 'address_ids[]';
        }
        if(!empty($address_new)){
            foreach ($address_new as $k=>$v){
                for ($i=0;$i<count($address);$i++){
                    if(substr($address_new[$k]['id'],0,3)==substr($address[$i]['id3'],0,3)){
                        $address_now['id'] = $address[$i]['id3'];
                        $address_now['title'] = $address[$i]['name3'];
                        $address_now['field'] = 'address_ids[]';
                        $address_new[$k]['children'][] = $address_now;
                    }
                }
                foreach ($address_new[$k]['children'] as $ksa=>$vs){
                    $address_two_new[$k][$address_new[$k]['children'][$ksa]['id']]['id'] = $address_new[$k]['children'][$ksa]['id'];
                    $address_two_new[$k][$address_new[$k]['children'][$ksa]['id']]['title'] = $address_new[$k]['children'][$ksa]['title'];
                    $address_two_new[$k][$address_new[$k]['children'][$ksa]['id']]['field'] = 'address_ids[]';
                }

                if(!empty($address_two_new)){
                    foreach ($address_two_new as $kss=>$vss){
                        if($kss == $address_new[$k]['id']){
                            $address_new[$k]['children'] = $address_two_new[$kss];
                        }
                    }
                }
                $address_new[$k]['children'] = array_merge($address_new[$k]['children']);
                foreach ($address_new[$k]['children'] as $ks=>$vs){
                    for ($i=0;$i<count($address);$i++){
                        if(substr($address_new[$k]['children'][$ks]['id'],0,4)==substr($address[$i]['id4'],0,4)){
                            if(in_array($address[$i]['id4'],$data)){
                                $address_now['checked'] = true;
                            }
                            $address_now['id'] = $address[$i]['id4'];
                            $address_now['title'] = $address[$i]['name4'];
                            $address_now['field'] = 'address_ids[]';
                            $address_new[$k]['children'][$ks]['children'][] = $address_now;
                        }
                    }
                    foreach ($address_new[$k]['children'][$ks]['children'] as $kss=>$vss){
                        $address_three_new[$ks][$address_new[$k]['children'][$ks]['children'][$kss]['id']]['id'] = $address_new[$k]['children'][$ks]['children'][$kss]['id'];
                        $address_three_new[$ks][$address_new[$k]['children'][$ks]['children'][$kss]['id']]['title'] = $address_new[$k]['children'][$ks]['children'][$kss]['title'];
                        $address_three_new[$ks][$address_new[$k]['children'][$ks]['children'][$kss]['id']]['field'] = 'address_ids[]';

                    }
                    if(!empty($address_three_new)){
                        foreach ($address_three_new as $kss=>$vss){
                            if($kss == $address_new[$k]['children'][$ks]['id']){
                                $address_new[$k]['children'][$ks]['children'] = array_merge($address_three_new[$kss]);
                            }
                        }
                    }
                }
            }
        }
    }
//    dump($address_new);die;
    return $address_new;
}
function address_funs($data=array())
{
//    $address = db('address')->field('id2 as id, name2 as title')->where(['id1'=>1,'id2'=>['in',[110000,120000,130000]]])->group('id')->select();
    $address = db('address')->field('id2 as id, name2 as title')->where(['id1'=>1,'status'=>1])->group('id')->select();
    if(!empty($address)){
        foreach ($address as $k=>$v){
            $address[$k]['field'] = 'address_ids[]';
            $address[$k]['children'] = db('address')->where('id2',$address[$k]['id'])->field('id3 as id, name3 as title')->group('id')->select();
            if(!empty($address[$k]['children'])){
                foreach ($address[$k]['children'] as $ks=>$vs){
                    $address[$k]['children'][$ks]['field'] = 'address_ids[]';
                    $address[$k]['children'][$ks]['children'] = db('address')->where('id3',$address[$k]['children'][$ks]['id'])->field('id4 as id, name4 as title')->group('id')->select();
                    if(!empty($address[$k]['children'][$ks]['children'])){
                        foreach($address[$k]['children'][$ks]['children'] as $kss=>$vss){
                            $address[$k]['children'][$ks]['children'][$kss]['field'] = 'address_ids[]';
                            if(!empty($address)){
                                if(in_array($address[$k]['children'][$ks]['children'][$kss]['id'],$data)){
                                    $address[$k]['children'][$ks]['children'][$kss]['checked'] = true;
                                }
                            }
                            if(empty($address[$k]['children'][$ks]['children'][$kss]['id'])){
                                unset($address[$k]['children'][$ks]['children'][$kss]);
                            }
                        }
                    }
                }
            }
        }
    }
    return $address;
}