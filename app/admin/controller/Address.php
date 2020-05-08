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


namespace app\admin\controller;

use think\Db;
use app\admin\model\SckClient as ClientModel;

class Address extends Permissions
{
    public function position()
    {
        $id = $this->request->has('region_id') ? $this->request->param('region_id', 0) : 0;
        if($id!==null){
            $new_id= db('address_old')->where('region_name',$id)->value('region_id');
            if(!empty($new_id)){
                $data['data']= db('address_old')->where('parent_id',$new_id)->select();
                if(!empty($data['data'])){
                    $data['code'] = 1;
                    return $data;
                }else{
                    return $this->error();
                }
            }else{
                return $this->error();
            }
        }else{
            return $this->error();
        }
    }
    public function address()
    {
        $id = $this->request->has('address_id') ? $this->request->param('address_id', 0) : 0;
        if($id!==null){
            $address = db('address')->field('id3 as address_id, name3 as address_name')->where('id2',$id)->group('address_id')->select();
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
            $address = db('address')->field('id4 as address_id, name4 as address_name')->where('id3',$id)->group('address_id')->select();
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
