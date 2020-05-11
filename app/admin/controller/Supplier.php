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
use app\admin\model\SckSupplier as SupplierModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;

class Supplier extends Permissions
{
    public function index()
    {
        $model = new SupplierModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['supplier_name|supplier_phone'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            : $model->where($where)
                ->order('create_time desc')
                ->paginate(20,false,['query'=>$this->request->param()]);

        $this->assign('data',$data);
        return $this->fetch();
    }


    public function publish()
    {
        $supplier_id = $this->request->has('supplier_id') ? $this->request->param('supplier_id', 0, 'intval') : 0;
        $model = new SupplierModel();

//        $address = db('address_old')->where('parent_id',1)->select();

        $id = Session::get('admin');
        if(!empty($id)){
            $user_info = \app\admin\model\Admin::get($id);
            if(!empty($user_info->address_ids)){
                $address_ids = json_decode($user_info->address_ids,true);
            }else{
                $address_ids =[];
            }
        }else{
            $address_ids =[];
        }

        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            ->where(['id1'=>1,'status'=>1,'id4'=>['in',$address_ids]])
            ->group('address_id')
            ->select();
        $this->assign('View_address',$address);

        if($supplier_id > 0) {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['supplier_name', 'require', '客户姓名不能为空'],
                    ['supplier_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if(false == $model->allowField(true)->save($post,['supplier_id'=>$supplier_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->supplier_id);
                    return $this->success('修改信息成功','admin/supplier/index');
                }
            } else {
                $data = $model->where('supplier_id',$supplier_id)->find();
                $this->assign('data',$data);
                return $this->fetch();
            }
        } else {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['supplier_name', 'require', '客户姓名不能为空'],
                    ['supplier_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $post['create_time'] = time();
                if(false == $model->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->supplier_id);
                    return $this->success('添加成功','admin/supplier/index');
                }
            } else {
                return $this->fetch();
            }
        }
    }

    /**
     * 客户删除
     * @throws Exception
     * @throws PDOException
     */
    public function delete()
    {
        if($this->request->isAjax()) {
            $supplier_id = $this->request->has('supplier_id') ? $this->request->param('supplier_id', 0, 'intval') : 0;
            $model = new SupplierModel();
            if(false == $model->where('supplier_id',$supplier_id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($supplier_id);//写入日志
                return $this->success('删除成功','admin/supplier/index');
            }
        }
    }

}
