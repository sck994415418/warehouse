<?php
namespace app\admin\controller;

use \think\Db;
use \think\Cookie;
use app\admin\controller\Permissions;
class Main extends Permissions
{
    public function index()
    {
        //tplay版本号
        $info['tplay'] = TPLAY_VERSION;
        //tp版本号
        $info['tp'] = THINK_VERSION;
        //php版本
        $info['php'] = PHP_VERSION;
        //操作系统
        $info['win'] = PHP_OS;
        //最大上传限制
        $info['upload_size'] = ini_get('upload_max_filesize');
        //脚本执行时间限制
        $info['execution_time'] = ini_get('max_execution_time').'S';
        //环境
        $sapi = php_sapi_name();
        if($sapi = 'apache2handler') {
        	$info['environment'] = 'apache';
        } elseif($sapi = 'cgi-fcgi') {
        	$info['environment'] = 'cgi';
        } else {
        	$info['environment'] = 'cli';
        }
        //剩余空间大小
        //$info['disk'] = round(disk_free_space("/")/1024/1024,1).'M';
        $this->assign('info',$info);


        /**
         *网站信息
         */
        $web['user_num'] = Db::name('admin')->count();
        $ip_ban = Db::name('webconfig')->value('black_ip');
        $web['ip_ban'] = empty($ip_ban) ? 0 : count(explode(',',$ip_ban));
        
        $web['file_num'] = Db::name('attachment')->count();
        $web['status_file'] = Db::name('attachment')->where('status',0)->count();
        $web['ref_file'] = Db::name('attachment')->where('status',-1)->count();
        $web['message_num'] = Db::name('messages')->count();
        $web['look_message'] = Db::name('messages')->where('is_look',0)->count();


        //登陆次数和下载次数
        $today = date('Y-m-d');

        //取当前时间的前十四天
        $date = [];
        $date_string = '';
        for ($i=9; $i >0 ; $i--) { 
            $date[] = date("Y-m-d",strtotime("-{$i} day"));
            $date_string.= date("Y-m-d",strtotime("-{$i} day")) . ',';
        }
        $date[] = $today;
        $date_string.= $today;
        $web['date_string'] = $date_string;

        $login_sum = '';
        foreach ($date as $k => $val) {
            $min_time = strtotime($val);
            $max_time = $min_time + 60*60*24;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
            $login_sum.= Db::name('admin_log')->where(['admin_menu_id'=>50])->where($where)->count() . ',';
        }
        $web['login_sum'] = $login_sum;

        $this->assign('web',$web);

        //库存不足
        $good_warn = db('sck_warehouse_good')->where(['good_delete'=>0])->field('create_time,good_warn_day,good_id,good_number,good_warn')->select();
        if(!empty($good_warn)){
            $warn_arr =[];
            $warn_day_arr =[];
            foreach ($good_warn as $k=>$v){
                if($good_warn[$k]['good_number']<=0 || $good_warn[$k]['good_number']<=$good_warn[$k]['good_warn']){
                    $warn_arr[] = $good_warn[$k]['good_id'];
                }
                if($good_warn[$k]['good_warn_day']>0){
                    $start_time = strtotime(date('Y-m-d H:i:s',time()-3600*24*$good_warn[$k]['good_warn_day']));
                    $end_time = strtotime(date('Y-m-d H:i:s',time()));
                    if($start_time>$good_warn[$k]['create_time']){
                        $have = db('sck_warehouse_good_log')
                            ->where(['good_id'=>$good_warn[$k]['good_id'],'good_status'=>2])
                            ->whereTime('create_time','between',[$start_time,$end_time])
                            ->find();
                        if(empty($have)){
                            $warn_day_arr[] = $good_warn[$k]['good_id'];
                        }
                    }

                }
            }
        }else{
            $warn_arr = [];
            $warn_day_arr = [];
        }
        $good_warn_count = count($warn_arr);
        $good_warn_day_count = count($warn_day_arr);
        $this->assign('good_warn_count',$good_warn_count);
        $this->assign('good_warn_day_count',$good_warn_day_count);
        $this->assign('good_warn',json_encode($warn_arr));
        $this->assign('good_warn_day',json_encode($warn_day_arr));
        //客户数
        $client_count = db('sck_client')->count();
        $this->assign('client_count',$client_count);
        return $this->fetch();
    }
}
