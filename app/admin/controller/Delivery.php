<?php

namespace app\admin\controller;

class Delivery extends Permissions{
    public function log_print(){
        $id = request()->get();
        $data = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id'=>['in',$id['data']]])
            ->join('sck_client sc','swgl.client_id = sc.client_id')
            ->field("swgl.*,sc.client_name,sc.client_company,sc.client_desc,sc.client_phone,sc.client_wechat")
            ->select();
        if($data){
            return view("",['data'=>$data]);
        }else{
            $this->error("未知错误,请重新选择");
        }
    }
    public function index(){
        return view();
    }

}