<?php

namespace app\admin\controller;

use think\Session;

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
            ->join('address ads','ads.id5 = sc.client_position_id or ads.id4 = sc.client_position_id','left')
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_warehouse_good_log_pay swglp','swglp.log_id = swgl.log_id','left')
            ->field("swgl.*,sc.client_id,sc.client_name,sc.client_company,sc.client_desc,sc.client_phone,sc.client_wechat,ads.name2,ads.name3,ads.name4,ads.name5,sc.client_position_details,swg.good_sku,swg.good_coding,swglp.pay_price,swglp.pay_total")
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

                        ];
                        $data[$k]['pay_total'] += empty($val['pay_total'])?$val['good_total']:$val['pay_total'];
                        $data[$k]['pay_price'] += empty($val['pay_price'])?0:$val['pay_price'];
                        unset($arr[$key]);
                        unset($data[$key]);

                    }
                }
                $data[$k]['admin_id'] = db('admin')->where('id',$v['admin_id'])->value('nickname');
                $data[$k]['admin_id_name'] = db('admin')->where('id',Session::get('admin'))->value('nickname');
            }
        }
        if ($data) {
            addlog();
            db("sck_warehouse_good_log")->where(['log_id'=>['in',$id['data']]])->setInc('is_print',1);
            return view("", ['data' => $data]);
        } else {
            $this->error("未知错误,请重新选择");
        }
    }
}