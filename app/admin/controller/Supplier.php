<?php

namespace app\admin\controller;

use app\admin\model\SckSupplier;
use app\admin\model\SckWarehouseGoodLog;
use think\Db;
use app\admin\model\SckSupplier as SupplierModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;
use app\admin\model\Admin as adminModel;//管理员模型

class Supplier extends Permissions
{
    public function index()
    {
        $model = new SupplierModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['supplier_name|supplier_phone'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if(isset($post['brand']) && !empty($post['brand'])){
            $where['supplier_brand'] = ['like','%'.$post['brand'].'%'];
        }
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }
        if (isset($post['time']) and !empty($post['time'])) {
            $start_time = strtotime(substr($post['time'],0,strripos($post['time'],' - ')));
            $end_time = strtotime(substr($post['time'],strripos($post['time'],' - ')+3));
            $where['create_time']=['between',[$start_time,$end_time]];
        }
        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            ->each(function ($k,$v){
//                if(!empty($k['supplier_category'])){
//                    $k['supplier_category'] = json_decode($k['supplier_category'],true);
//                    $admin_id = Session::get('admin');
//                    if (!isset($admin_id)) {
//                        return $this->error('未检测到登录状态，请重新登陆!');
//                    }
//                    $user_info = adminModel::get($admin_id);
//                    if(!empty($user_info['admin_supplier_ids'])){
//                        $user_info['admin_supplier_ids'] = json_decode($user_info['admin_supplier_ids'],true);
//                    }else{
//                        $user_info['admin_supplier_ids'] = [];
//                    }
//                    foreach ($k['supplier_category'] as $ks=>$vs){
//                        if(!array_intersect($k['supplier_category'],$user_info['admin_supplier_ids'])){
//                            $k['yes'] = 0;
//                        }else{
//                            $k['yes'] = 1;
//                        }
//                    }
//                }else{
//                    $k['yes'] = 0;
//                }
            })
            : $model->where($where)
                ->order('create_time desc')
                ->paginate(20,false,['query'=>$this->request->param()])
        ->each(function ($k,$v){
//            if(!empty($k['supplier_category'])){
//                $k['supplier_category'] = json_decode($k['supplier_category'],true);
//                $admin_id = Session::get('admin');
//                if (!isset($admin_id)) {
//                    return $this->error('未检测到登录状态，请重新登陆!');
//                }
//                $user_info = adminModel::get($admin_id);
//                if(!empty($user_info['admin_supplier_ids'])){
//                    $user_info['admin_supplier_ids'] = json_decode($user_info['admin_supplier_ids'],true);
//                }else{
//                    $user_info['admin_supplier_ids'] = [];
//                }
//                foreach ($k['supplier_category'] as $ks=>$vs){
//                    if(!array_intersect($k['supplier_category'],$user_info['admin_supplier_ids'])){
//                        $k['yes'] = 0;
//                    }else{
//                        $k['yes'] = 1;
//                    }
//                }
//            }else{
//                $k['yes'] = 0;
//            }
        });
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function publish()
    {
        $supplier_id = $this->request->has('supplier_id') ? $this->request->param('supplier_id', 0, 'intval') : 0;
        $model = new SupplierModel();
        if($supplier_id > 0) {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['supplier_name', 'require', '客户姓名不能为空'],
                    ['supplier_phone', 'require', '客户电话不能为空'],
//                    ['supplier_category', 'require', '请选择销售类目'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if(!empty($post['supplier_category'])){
                    $post['supplier_category'] = json_encode($post['supplier_category']);
                }
                if(false == $model->allowField(true)->save($post,['supplier_id'=>$supplier_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->supplier_id);
                    return $this->success('修改信息成功','admin/supplier/index');
                }
            } else {
                $data = $model->where('supplier_id',$supplier_id)->find();
//                dump($data);die;
                $category = db('sck_warehouse_good_category')->field('category_id as id,category_name as name,parent_id')->select();
                if(!empty($data['supplier_category'])){
                    $data['supplier_category'] = json_decode($data['supplier_category'],true);
                }
                $category = getrole($category);
                if(!empty($category)){
                    foreach ($category as $ks=>$vs){
                        $category[$ks]['open'] = 'false';
                        if(!empty($category[$ks]['children'])){
                            foreach ($category[$ks]['children'] as $kss=>$vss){
                                $category[$ks]['children'][$kss]['open'] = 'false';
                                if(!empty($category[$ks]['children'][$kss]['children'])){
                                    foreach ($category[$ks]['children'][$kss]['children'] as $ksss=>$vsss){
                                        $category[$ks]['children'][$kss]['children'][$ksss]['field'] = 'supplier_category[]';
                                        $category[$ks]['children'][$kss]['children'][$ksss]['open'] = false;
                                        if(!empty($data['supplier_category'])) {
                                            if (in_array($category[$ks]['children'][$kss]['children'][$ksss]['id'], $data['supplier_category'])) {
                                                $category[$ks]['children'][$kss]['children'][$ksss]['checked'] = true;
                                            }
                                        }

                                    }

                                }

                            }

                        }

                    }

                }
                $this->assign('category',json_encode($category));
                $this->assign('data',$data);
                return $this->fetch();
            }
        } else {
            if($this->request->isPost()) {
                $post = $this->request->post();

                $validate = new Validate([
                    ['supplier_name', 'require', '客户姓名不能为空'],
                    ['supplier_phone', 'require', '客户电话不能为空'],
                    ['supplier_category', 'require', '请选择销售类目'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $post['create_time'] = time();
                if(!empty($post['supplier_category'])){
                    $post['supplier_category'] = json_encode($post['supplier_category']);
                }
                if(false == $model->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->supplier_id);
                    return $this->success('添加成功','admin/supplier/index');
                }

            } else {
                $address = db('address')
                    ->field('id1,id2 as address_id, name2 as address_name')
                    ->where(['id1' => 1, 'status' => 1])
                    ->group('address_id')
                    ->select();
                $this->assign('View_address', $address);
                $category = db('sck_warehouse_good_category')->field('category_id as id,category_name as name,parent_id')->select();
                if(!empty($data['supplier_category'])){
                    $data['supplier_category'] = json_decode($data['supplier_category'],true);
                }
                $category = getrole($category);
                if(!empty($category)){
                    foreach ($category as $ks=>$vs){
                        $category[$ks]['open'] = 'false';
                        if(!empty($category[$ks]['children'])){
                            foreach ($category[$ks]['children'] as $kss=>$vss){
                                $category[$ks]['children'][$kss]['open'] = 'false';
                                if(!empty($category[$ks]['children'][$kss]['children'])){
                                    foreach ($category[$ks]['children'][$kss]['children'] as $ksss=>$vsss){
                                        $category[$ks]['children'][$kss]['children'][$ksss]['field'] = 'supplier_category[]';
                                        $category[$ks]['children'][$kss]['children'][$ksss]['open'] = false;
                                    }

                                }

                            }

                        }

                    }

                }
                $this->assign('category', json_encode($category));
                return $this->fetch();
            }
        }
    }

    /**
     * 供应商删除
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
    public function supplier_details()
    {
        $supplier_id = $this->request->has('supplier_id') ? $this->request->param('supplier_id', 0, 'intval') : 0;
        $goods = Request()->get('good_id');
        if(!empty($supplier_id)){
            $model = new SckSupplier();
            $supplier = $model->get(['supplier_id'=>$supplier_id]);

        }elseif(!empty($goods)){
            $supplier_id = (new SckWarehouseGoodLog())->where(['good_id'=>$goods])->value('supplier_id');
            $model = new SckSupplier();
            $supplier = $model->get(['supplier_id'=>$supplier_id]);
        }else{
            return $this->error('页面错误，请重试！');
        }
        if(!empty($supplier)){
            if(!empty($supplier['supplier_category'])){
                $supplier_category = json_decode($supplier['supplier_category'],true);
                $supplier['supplier_category'] = db('sck_warehouse_good_category')->where('category_id','in',$supplier_category)->column('category_name');
            }
            $this->assign('supplier',$supplier);
            return $this->fetch();
        }else{
            return $this->error('未找到该供应商！');
        }
    }
}
