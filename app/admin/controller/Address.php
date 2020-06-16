<?php

namespace app\admin\controller;

use think\Db;
use app\admin\model\SckClient as ClientModel;
use app\admin\model\Address as AddressModel;
use think\Session;

class Address extends Permissions
{
    public function index(){
        $address = db('address')->field('status,id1,id2 as address_id, name2 as address_name')->where('id1',1)->group('address_id')->select();
        $this->assign('View_address',$address);
        return $this->fetch();
    }
    public function status_on()
    {
        if($this->request->isAjax()) {
            $model = new AddressModel();
            $id = $this->request->has('id') ? $this->request->param('id', 0) : 0;
//            dump($id);die;
            if($id>0) {
                if(false == $model->allowField(true)->save(['status'=>1],['id2'=>$id])) {
                    return $this->error('开启失败');
                } else {
                    addlog($id);
                    return $this->success('开启成功');
                }
            } else {
                return $this->error('页面错误，请刷新后重试！');
            }
        }
    }
    public function status_off()
    {
        if($this->request->isAjax()) {
            $model = new AddressModel();
            $id = $this->request->has('id') ? $this->request->param('id', 0) : 0;
//            dump($id);die;
            if($id>0) {
                if(false == $model->allowField(true)->save(['status'=>0],['id2'=>$id])) {
                    return $this->error('禁用失败');
                } else {
                    addlog($id);
                    return $this->success('禁用成功');
                }
            } else {
                return $this->error('页面错误，请刷新后重试！');
            }
        }
    }
    public function address()
    {
        $id = $this->request->has('address_id') ? $this->request->param('address_id', 0) : 0;
        if($id!==null){
            //================================================================================================
//            $address_id = Session::get('admin');
//            if(!empty($address_id)){
//                $user_info = \app\admin\model\Admin::get($address_id);
//                if(!empty($user_info->address_ids)){
//                    $address_ids = json_decode($user_info->address_ids,true);
//                }else{
//                    $address_ids =[];
//                }
//            }else{
//                $address_ids =[];
//            }
//            $address_ids = db('address')->where(['id4'=>['in',$address_ids]])->group('id3')->column('id3');
            //================================================================================================
            $address = db('address')
                ->field('id3 as address_id, name3 as address_name')
                ->where(['id2'=>$id])
                //================================================================================================
//                ->where(['id3'=>['in',$address_ids]])
                //================================================================================================
                ->group('address_id')
                ->select();
            if(!empty($address[0]['address_id'])){
                $data['code'] = 1;
                $data['data'] = $address;
                return $data;
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }

    }

    public function address_qu()
    {
        $id = $this->request->has('address_id') ? $this->request->param('address_id', 0) : 0;
        if($id!==null){
            //================================================================================================
//            $address_id = Session::get('admin');
//            if(!empty($address_id)){
//                $user_info = \app\admin\model\Admin::get($address_id);
//                if(!empty($user_info->address_ids)){
//                    $address_ids = json_decode($user_info->address_ids,true);
//                }else{
//                    $address_ids =[];
//                }
//            }else{
//                $address_ids =[];
//            }
            //================================================================================================
            $address = db('address')
                ->field('id4 as address_id, name4 as address_name')
                ->where(['id3'=>$id])
                //================================================================================================
//                ->where(['id4'=>['in',$address_ids]])
                //================================================================================================
                ->group('address_id')
                ->select();
            if(!empty($address[0]['address_id'])){
                $data['code'] = 1;
                $data['data'] = $address;
                return $data;
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }

    }

    public function address_street()
    {
        $id = $this->request->has('address_id') ? $this->request->param('address_id', 0) : 0;
        if($id!==null){
            $address = db('address')->where('id4',$id)->field('id5 as address_id, name5 as address_name')->group('address_id')->select();
            if(!empty($address[0]['address_id'])){
                $data['code'] = 1;
                $data['data'] = $address;
                return $data;
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }

    }
}
