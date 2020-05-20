<?php

namespace app\admin\controller;

use think\Db;
use app\admin\model\SckWarehouseGoodCategory as WarehouseGoodCategoryModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;

class WarehouseGoodCategory extends Permissions
{
    public function index()
    {
        $data = db('sck_warehouse_good_category')->field('category_id as id,category_name as title,parent_id')->select();
        if(!empty($data)){
            $data = getrole($data);
        }
        $this->assign('data',json_encode($data));
        return $this->fetch();
    }


    public function publish()
    {
        $category_id = $this->request->has('category_id') ? $this->request->param('category_id', 0, 'intval') : 0;
        $category_name = $this->request->has('category_name') ? $this->request->param('category_name') : 0;
        $model = new WarehouseGoodCategoryModel();
        if($category_id > 0) {
            if($this->request->isGet()) {
//                $post = $this->request->post();
//                $validate = new Validate([
//                    ['category_name', 'require', '类型名称不能为空'],
//                ]);
//                if (!$validate->check($post)) {
//                    $this->error('提交失败：' . $validate->getError());
//                }
                if(false == $model->allowField(true)->save(['category_name'=>$category_name],['category_id'=>$category_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($category_id);
                    return $this->success('修改成功','admin/WarehouseGoodCategory/index');
                }
            } else {
                $data = $model->where('category_id',$category_id)->find();
                $this->assign('data',$data);
                return $this->fetch();
            }
        } else {
            $parent_id = $this->request->has('parent_id') ? $this->request->param('parent_id', 0, 'intval') : 0;
            if($this->request->isGet()) {
                if(false == $model->allowField(true)->save(['parent_id'=>$parent_id,'category_name'=>'未命名'])) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->category_id);
                    return $this->success('添加成功','admin/WarehouseGoodCategory/index');
                }
            } else {
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
            $category_id = $this->request->has('category_id') ? $this->request->param('category_id', 0, 'intval') : 0;
            $model = new WarehouseGoodCategoryModel();
            if(false == $model->where('category_id',$category_id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($category_id);//写入日志
                return $this->success('删除成功','admin/WarehouseGoodCategory/index');
            }
        }
    }

    public function two()
    {
        $category_id = $this->request->has('category_id') ? $this->request->param('category_id', 0, 'intval') : 0;
        $category = db('sck_warehouse_good_category')
            ->where(['parent_id'=>$category_id])
            ->select();
        if(!empty($category)){
            $data['code'] = 1;
            $data['data'] = $category;
            return $data;
        }else{
            return $this->error();
        }
    }
    public function three()
    {
        $category_id = $this->request->has('category_id') ? $this->request->param('category_id', 0, 'intval') : 0;
        $category = db('sck_warehouse_good_category')
            ->where(['parent_id'=>$category_id])
            ->select();
        if(!empty($category)){
            $data['code'] = 1;
            $data['data'] = $category;
            return $data;
        }else{
            return $this->error();
        }
    }
}
