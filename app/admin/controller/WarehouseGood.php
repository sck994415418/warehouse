<?php

namespace app\admin\controller;

use app\admin\model\SckWarehouseGoodLog;
use think\Db;
use app\admin\model\SckWarehouseGood as WarehouseGoodModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;

class WarehouseGood extends Permissions
{
    public function index()
    {
        $model = new WarehouseGoodModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['good_name'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }

        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            : $model->where($where)
                ->order('create_time desc')
                ->paginate(20, false, ['query' => $this->request->param()]);

        $this->assign('data', $data);
        return $this->fetch();
    }


    public function publish()
    {
        $model = new WarehouseGoodModel();
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new Validate([
                ['good_name', 'require', '商品名称不能为空'],
                ['good_price', 'require', '商品单价不能为空'],
                ['good_total', 'require', '商品总价不能为空'],
                ['good_amount', 'require', '商品库存不能为空'],
            ]);
            $post['good_number'] = $post['good_amount'];
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $post['create_time'] = time();
            Db::startTrans();
            try {
                $ok = $model->allowField(true)->save($post);
                $good_id = Db::name('sck_warehouse_good')->getLastInsID();
                if($ok){
                    $LogModel = new SckWarehouseGoodLog();
                    $post['good_id'] = $good_id;
                    $insert = $LogModel->allowField(true)->save($post);
                    if($insert){
                        addlog($good_id);
                        $json = ['code'=>1,'msg'=>'添加成功','url'=>'index'];
                    }else{
                        throw new \Exception('添加失败，请重试!');
                    }
                }else{
                    throw new \Exception('添加失败，请重试!');
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $json = ['code'=>0,'msg'=>$e->getMessage()];
            }
            return $json;
        } else {
            return $this->fetch();
        }
    }

    public function edit()
    {
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        $model = new WarehouseGoodModel();
        if ($good_id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['good_name', 'require', '商品名称不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }

                Db::startTrans();
                try {
                    $ok = $model->allowField(true)->save($post, ['good_id' => $good_id]);
                    if($ok){
                        $LogModel = new SckWarehouseGoodLog();
                        $insert = $LogModel->allowField(true)->save($post, ['good_id' => $good_id]);
                        if($insert){
                            addlog($good_id);
                            $json = ['code'=>1,'msg'=>'修改成功','url'=>'index'];
                        }else{
                            throw new \Exception('修改失败，请重试!');
                        }
                    }else{
                        throw new \Exception('修改失败，请重试!');
                    }
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $json = ['code'=>0,'msg'=>$e->getMessage()];
                }
                return $json;

            } else {
                $data = $model->where('good_id', $good_id)->find();
                $this->assign('data', $data);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    /**
     * 供应商删除
     * @throws Exception
     * @throws PDOException
     */
    public function delete()
    {
        if ($this->request->isAjax()) {
            $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
            $model = new WarehouseGoodModel();
            if (false == $model->where('good_id', $good_id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($good_id);//写入日志
                return $this->success('删除成功', 'admin/WarehouseGood/index');
            }
        }
    }

}
