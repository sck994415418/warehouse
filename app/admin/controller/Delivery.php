<?php

namespace app\admin\controller;

use app\admin\model\SckWarehouseGood as WarehouseGoodModel;
use app\admin\model\SckWarehouseGoodLog;
use think\Db;
use think\Session;
use app\admin\controller\Exel;
use PHPExcel;
class Delivery extends Permissions
{
//    打印销售单
    public function log_print()
    {
        $id = request()->get();
        $data = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_client sc', 'swgl.client_id = sc.client_id')
//            ->join('address ads','ads.id5 = sc.client_position_id or ads.id4 = sc.client_position_id','left')
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_warehouse_good_log_pay swglp','swglp.log_id = swgl.log_id','left')
            ->field("swgl.*,sc.client_id,sc.client_name,sc.client_company,sc.client_desc,sc.client_phone,sc.client_wechat,sc.client_position_details,swg.good_sku,swg.good_coding,swg.good_position,swglp.pay_price,swglp.pay_total")
            ->select();
        $arr = $data;
        foreach($data as $k=>$v){
            if(isset($arr[$k])){
                $data[$k]['goods'][] =[
                    'good_id'=>$v['good_id'],
                    'good_name'=>$v['good_name'],
                    'good_desc'=>$v['good_desc'],
                    'good_number'=>$v['good_number'],
                    'good_amount'=>$v['good_amount'],
                    'good_price'=>$v['good_price'],
                    'good_total'=>$v['good_total'],
                    'good_sku'=>$v['good_sku'],
                    'good_coding'=>$v['good_coding'],
                    'good_position'=>$v['good_position'],
                ];
                $data[$k]['pay_total'] = empty($v['pay_total'])?$v['good_total']:$v['pay_total'];
                $data[$k]['pay_price'] =empty($v['pay_price'])?0:$v['pay_price'];
                unset($data[$k]['good_id'],$data[$k]['good_name'],$data[$k]['good_desc'],$data[$k]['good_number'],$data[$k]['good_amount'], $data[$k]['good_price'], $data[$k]['good_total']);

                foreach($arr as $key=>$val){
                    unset($arr[$k]);
                    if($v['client_id'] == $val['client_id'] && $v['log_id'] != $val['log_id']){
                        $data[$k]['goods'][] = [
                            'good_id'=>$val['good_id'],
                            'good_name'=>$val['good_name'],
                            'good_desc'=>$val['good_desc'],
                            'good_number'=>$val['good_number'],
                            'good_amount'=>$val['good_amount'],
                            'good_price'=>$val['good_price'],
                            'good_total'=>$val['good_total'],
                            'good_sku'=>$val['good_sku'],
                            'good_coding'=>$val['good_coding'],
                            'good_position'=>$val['good_position'],
                        ];
                        $data[$k]['pay_total'] += empty($val['pay_total'])?$val['good_total']:$val['pay_total'];
                        $data[$k]['pay_price'] += empty($val['pay_price'])?0:$val['pay_price'];
                        unset($arr[$key]);
                        unset($data[$key]);
                    }
                }
                $data[$k]['admin_id'] = db('admin')->where('id',$v['admin_id'])->value('nickname');
                $data[$k]['admin_id_name'] = db('admin')->where('id',Session::get('admin'))->value('nickname');
                $data[$k]['company'] = db('sck_warehouse_good_log')->where('log_id',$v['log_id'])->value('company');
            }
        }
        if ($data) {
            addlog();
            db("sck_warehouse_good_log")->where(['log_id'=>['in',$id['data']]])->setInc('is_print',1);
            return view("", ['data' => $data]);
        } else {
            $this->error("请联系库管出库后再次操作");
        }
    }
    //商品出库
    public function good_out(){
        if(request()->isPost()){
            $data = request()->post();
            $data = db('sck_warehouse_good_log')
                ->alias('swgl')
                ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
                ->where(['log_id' => ['in', $data['data']],'is_delivery'=>0])
                ->update(['swgl.is_delivery'=>1]);
            if($data){
                addlog($data['data']);
                $this->success("出库成功");
            }else{
                $this->error("出库失败");
            }
        }else{
            $this->error("请求错误");
        }

    }
    //拒绝出库
    public function good_out_no(){
        if(request()->isPost()){
            $data = request()->post();
            Db::startTrans();
            try {
                $ok = db('sck_warehouse_good_log')
                    ->alias('swgl')
                    ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
                    ->where(['log_id' => ['in', $data['data']],'is_delivery'=>0])
                    ->update(['swgl.is_delivery'=>2,'swg.good_number' => WarehouseGoodModel::raw('swg.good_number+swgl.good_amount')]);
                if ($ok) {
                    $insert = db('sck_warehouse_good_log_pay')
                        ->where(['log_id' => ['in', $data['data']],'pay_status'=>2])
                        ->update(['pay_status' => 4]);
                    if ($insert) {
                        addlog(json($data['data']));
                        $json = ['code' => 1, 'msg' => '拒绝出库成功！', 'url' => ''];
                    } else {
                        throw new \Exception('拒绝出库失败，请重试!');
                    }
                } else {
                    throw new \Exception('拒绝出库失败，请重试!');
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $json = ['code' => 0, 'msg' => $e->getMessage()];
            }
            return $json;
        }else{
            $this->error("请求错误");
        }

    }
//    导出excel信息
    public function excel(){
        $id = request()->get();
        $arr = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_warehouse_good_log_pay swglp','swglp.log_id = swgl.log_id','left')
            ->field("swgl.*,swg.good_sku,swg.good_coding,swglp.pay_price,swglp.pay_total")
            ->select();
        if ($arr) {
            $head = ['序号', '货物或应税劳务、服务名称', '计量单位', '规格型号', '数量', '金额', '税率', '商品税目', '折扣金额', '税额','折扣税额','折扣率','单价','价格方式','税收分类编码版本号','税收分类编码','企业商品编码','使用优惠政策标识','零税率标识','优惠政策说明','中外合作油气田标识'];
            $keys = ['ids', 'good_name','unit','good_sku', 'good_amount', 'good_total', 'tax_rate', 'tax_items', 'discount', 'tax_break','tax_deduction','discount_rate','good_price','way','code_number','classification_code','commodity_code','policy_labels','Zero_rate_mark','explain','identification'];
            foreach($arr as $k=>$v){
                $data[$k]['ids'] = $k+1;
                $data[$k]['good_name'] = $v['good_name'];
                $data[$k]['unit'] = '个';
                $data[$k]['good_sku'] = $v['good_sku'];
                $data[$k]['good_amount'] = number_format($v['good_amount'],6);
                $data[$k]['good_total'] = number_format($v['good_total'],2);
                $data[$k]['tax_rate']= '0.13';
                $data[$k]['tax_items']= "";
                $data[$k]['discount']="";
                $data[$k]['tax_break']=round($v['good_total']/1.13*0.13,2);
                $data[$k]['tax_deduction']="";
                $data[$k]['discount_rate']="";
                $data[$k]['good_price']=number_format($v['good_price'],6);
                $data[$k]['way']=1;
                $data[$k]['code_number']='35.0';
                $data[$k]['classification_code']=0;
                $data[$k]['commodity_code']=0;
                $data[$k]['policy_labels']=0;
                $data[$k]['Zero_rate_mark']=0;
                $data[$k]['explain']=0;
                $data[$k]['identification']=0;
            }

            addlog();
            (new Exel())->excelExport('清单表',$head,$data);
//            (new Exel())->outdata('清单表',$data,$head,$keys);
        } else {
            $this->error("未知错误,请重新选择");
        }
    }

    //    导出excel信息
    public function excelall(){
        $id = request()->get();

        $arr = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_client sc','sc.client_id = swgl.client_id','left')
            ->field('swgl.*,swg.good_coding,swg.good_sku,sc.client_company')
            ->select();

        if ($arr) {
            $head = ['商品名称', '商品规格', '商品编码', '出货日期', '出货数量', '出货单价', '合计', '客户', '备注'];
            $keys=['good_name','good_sku','good_coding','create_time','good_amount','good_price','good_total','client_company','good_desc'];
            foreach($arr as $k=>$v){
                $data[$k]['good_name'] = $v['good_name'];
                $data[$k]['good_sku'] = $v['good_sku'];
                $data[$k]['good_coding'] = $v['good_coding'];
                $data[$k]['time'] = date('Y-m-d H:i:s',$v['create_time']);
                $data[$k]['good_amount'] = $v['good_amount'];
                $data[$k]['good_price'] =$v['good_price'];
                $data[$k]['good_total'] =$v['good_total'];
                $data[$k]['client']= $v['client_company'];
                $data[$k]['good_desc']=$v['good_desc'];
            }

            addlog();
            (new Exel())->excelExport('出库记录',$head,$data);
//            (new Exel())->outdata('清单表',$arr,$head,$keys);
        } else {
            $this->error("未知错误,请重新选择");
        }
    }

    //    导出excel信息
    public function inventory(){
        $id = request()->get();

//        $arr = db('sck_warehouse_good_log')
//            ->alias('swgl')
//            ->where(['swgl.log_id' => ['in', $id['data']]])
//            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
//            ->join('sck_client sc','sc.client_id = swgl.client_id','left')
//            ->field('swgl.*,swg.good_coding,swg.good_sku,sc.client_company')
//            ->select();
        $model = new SckWarehouseGoodLog();
        $arr = $model
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_warehouse_good swg', 'swg.good_id = swgl.good_id', 'LEFT')
            ->join('sck_warehouse_good_log_pay swgp', 'swgp.log_id = swgl.log_id', 'LEFT')
            ->join('sck_client sc', 'sc.client_id = swgl.client_id')
            ->field('swgl.*,swgl.log_id as id,swg.good_name,swg.good_price as goods_price,swg.good_total as goods_total,swg.tax_status,swg.good_brand,swgp.pay_price,swgp.pay_total,sc.client_name,sc.client_company')
            ->order('id desc')
            ->select();
        if ($arr) {
            $head = ['ID', '商品名称', '出库数量', '商品成本单价', '商品出库单价', '商品成本总价', '商品出库总价', '利润', '出库人','客户名称','客户公司','发票','出库公司','出库时间','已付金额','应付金额'];
            $keys=['id','good_name','good_amount','goods_price','good_price','good_total','lowest_price','nickname','client_name','client_company','tax_status','company','create_time','pay_price','pay_total',''];
            foreach($arr as $k=>$v){
                $data[$k]['id'] = $v['id'];
                $data[$k]['good_name'] = $v['good_name'];
                $data[$k]['good_amount'] = $v['good_amount'];
                $data[$k]['goods_price'] = $v['goods_price'];
                $data[$k]['good_price'] = $v['good_price'];
                $data[$k]['goods_amount'] =$v['good_amount']*$v['goods_price'];
                $data[$k]['good_total'] =$v['good_total'];
                $data[$k]['lowest_price']= $v['lowest_price'];
                $data[$k]['nickname']=$v['nickname']->nickname;
                $data[$k]['client_name']=$v['client_name'];
                $data[$k]['client_company']=$v['client_company'];
                $data[$k]['tax_status']=$this->tax($v['tax_status']);
                $data[$k]['company']=$v['company'];
                $data[$k]['create_time']=$v['create_time'];
                $data[$k]['pay_price']=$v['pay_price'];
                $data[$k]['pay_total']=$v['pay_total'];
            }
            addlog();
            (new Exel())->excelExport('出库记录',$head,$data);
//            (new Exel())->outdata('清单表',$arr,$head,$keys);
        } else {
            $this->error("未知错误,请重新选择");
        }
    }
    public function tax($status){
        switch ($status) {
            case "1":
                $agency = "专票";
                break;
            case "2":
                $agency = "专票1%";
                break;
            case "3":
                $agency = "普票";
                break;
            case "4":
                $agency = "无票";
                break;
            case "5":
                $agency = "专票3%";
                break;
            case "6":
                $agency = "专票6%";
                break;
            default:
                $agency = "未知";
                break;
        }
        return $agency;
    }


    //退货入库
    public function good_return(){
        if(request()->isPost()){
            $data = request()->post();
            $log = $data = db('sck_warehouse_good_log')
                ->where(['log_id' => ['in', $data['data']],'is_return_enter'=>0])
                ->select();
            $GoodLogModel = new SckWarehouseGoodLog();
            $error_arr = [];
            $success_arr = 0;
            $msg_data = null;
            $msg = null;
            Db::startTrans();
            foreach ($log as $k=>$v){
                try {
                    $ok = db('sck_warehouse_good_log')->where(['log_id'=>$log[$k]['log_id']])->update(['is_return_enter'=>1]);
                    if ($ok) {
                        $insert = db('sck_warehouse_good')
                            ->where(['good_id' => $log[$k]['good_id']])
                            ->update(['good_number' => WarehouseGoodModel::raw('good_number+' . intval($log[$k]['good_amount'] . ''))]);
                        $already_number = $GoodLogModel->where(['log_parent_id'=>$log[$k]['log_parent_id'],'good_id'=>$log[$k]['good_id'],'good_status'=>3])->sum('good_amount');
                        $GoodLogModel->where('log_id', $log[$k]['log_id'])->update(['good_desc' => "退货产品，共计退回" . $already_number]);
                        if ($insert) {
                            addlog($log[$k]['log_id']);
                            $success_arr++;
//                            $json = ['code' => 1, 'msg' => '退货入库成功！', 'url' => ''];
                        } else {
                            $error_arr[] = $log[$k]['log_id'];
//                            throw new \Exception('退货入库失败，请重试!');
                        }
                    } else {
                        $error_arr[] = $log[$k]['log_id'];
//                        throw new \Exception('退货入库失败，请重试!');
                    }
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();

                    $msg_data = $e->getMessage();
                }
            }
            if(!empty($error_arr)){
                $error_arr = implode($error_arr,',');
                $error_arr = 'ID为['.$error_arr.']的记录退货入库失败！';
            }else{
                $error_arr = '';
            }
            $msg = $success_arr.'条数据退货入库成功！<br><br>'.$error_arr;
            $json = ['code' => 0, 'msg' => $msg.$msg_data];
            return $json;

//            dump($log);die;
//            $data = db('sck_warehouse_good_log')
//                ->alias('swgl')
//                ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
//                ->where(['log_id' => ['in', $data['data']],'is_delivery'=>0])
//                ->update(['swgl.is_delivery'=>1]);
//            if($data){
//                addlog($data['data']);
//                $this->success("出库成功");
//            }else{
//                $this->error("出库失败");
//            }
        }else{
            $this->error("请求错误");
        }

    }
    //入库审批
    public function good_enter(){
        if(request()->isPost()){
            $data = request()->post();
            $log = $data = db('sck_warehouse_good_log')
                ->where(['log_id' => ['in', $data['data']],'is_good_enter'=>0])
                ->select();
            $error_arr = [];
            $success_arr = 0;
            $msg_data = null;
            $msg = null;
            $type = $this->request->has('type') ? $this->request->param('type', 0, 'intval') : 0;
            if(isset($type) and $type == 1){
                Db::startTrans();
                foreach ($log as $k=>$v){
                    try {
                        $ok = db('sck_warehouse_good_log')->where(['log_id'=>$log[$k]['log_id']])->update(['is_good_enter'=>1]);
                        if ($ok) {
                            $insert = db('sck_warehouse_good')
                                ->where(['good_id' => $log[$k]['good_id']])
                                ->update(['good_number' => WarehouseGoodModel::raw('good_number+' . intval($log[$k]['good_amount'] . '')), 'good_amount' => WarehouseGoodModel::raw('good_amount+' .$log[$k]['good_amount'] . '')]);
                            if ($insert) {
                                addlog($log[$k]['log_id']);
                                $success_arr++;
//                            $json = ['code' => 1, 'msg' => '退货入库成功！', 'url' => ''];
                            } else {
                                $error_arr[] = $log[$k]['log_id'];
//                            throw new \Exception('退货入库失败，请重试!');
                            }
                        } else {
                            $error_arr[] = $log[$k]['log_id'];
//                        throw new \Exception('退货入库失败，请重试!');
                        }
                        // 提交事务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();

                        $msg_data = $e->getMessage();
                    }
                }
                if(!empty($error_arr)){
                    $error_arr = implode($error_arr,',');
                    $error_arr = 'ID为['.$error_arr.']的记录入库失败！';
                }else{
                    $error_arr = '';
                }
                $msg = $success_arr.'条数据入库成功！<br><br>'.$error_arr;
                $json = ['code' => 0, 'msg' => $msg.$msg_data];
                return $json;
            }elseif(isset($type) and $type == 0){
                Db::startTrans();
                foreach ($log as $k=>$v){
                    try {
                        $ok = db('sck_warehouse_good_log')->where(['log_id'=>$log[$k]['log_id']])->update(['is_good_enter'=>2]);
                        if ($ok) {
                            $insert = db('sck_warehouse_good_log_pay')->where('log_id',$log[$k]['log_id'])->update(['pay_status'=>5]);
                            if ($insert) {
                                addlog($log[$k]['log_id']);
                                $success_arr++;
//                            $json = ['code' => 1, 'msg' => '退货入库成功！', 'url' => ''];
                            } else {
                                $error_arr[] = $log[$k]['log_id'];
//                            throw new \Exception('退货入库失败，请重试!');
                            }
                        } else {
                            $error_arr[] = $log[$k]['log_id'];
//                        throw new \Exception('退货入库失败，请重试!');
                        }
                        // 提交事务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();

                        $msg_data = $e->getMessage();
                    }
                }
                if(!empty($error_arr)){
                    $error_arr = implode($error_arr,',');
                    $error_arr = 'ID为['.$error_arr.']的记录拒绝入库失败！';
                }else{
                    $error_arr = '';
                }
                $msg = $success_arr.'条数据拒绝入库成功！<br><br>'.$error_arr;
                $json = ['code' => 0, 'msg' => $msg.$msg_data];
                return $json;
            }

        }else{
            $this->error("请求错误");
        }

    }

}