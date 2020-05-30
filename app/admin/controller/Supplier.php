<?php

namespace app\admin\controller;

use app\admin\model\SckSupplier;
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
        if (isset($post['time']) and !empty($post['time'])) {
            $start_time = strtotime(substr($post['time'],0,strripos($post['time'],' - ')));
            $end_time = strtotime(substr($post['time'],strripos($post['time'],' - ')+3));
            $where['create_time']=['between',[$start_time,$end_time]];
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
                $category = db('sck_warehouse_good_category')->field('category_id as id,category_name as title,parent_id')->select();
                if(!empty($category)){
                    $category = getrole($category);
                    if(!empty($category)){
                        foreach ($category as $k=>$v){
                            if(!empty($category[$k]['children'])){
                                foreach ($category[$k]['children'] as $ks=>$vs){
                                    if(!empty($category[$k]['children'][$ks]['children'])){
                                        foreach ($category[$k]['children'][$ks]['children'] as $kss=>$vss){
                                            $category[$k]['children'][$ks]['children'][$kss]['field'] = 'supplier_category[]';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $this->assign('View_category', json_encode($category));
                }
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
        if(!empty($supplier_id)){
            $model = new SckSupplier();
            $supplier = $model->get(['supplier_id'=>$supplier_id]);
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
        }else{
            return $this->error('页面错误，请重试！');
        }
    }
}
