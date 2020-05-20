<?php

namespace app\admin\controller;

use app\admin\model\SckClient;
use app\admin\model\SckWarehouseGoodLog;
use think\Db;
use app\admin\model\SckWarehouseGoodLog as WarehouseGoodLogModel;
use app\admin\model\SckWarehouseGood as WarehouseGoodModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;

class WarehouseGoodLog extends Permissions
{
    public function GoodEnter()
    {
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        if($good_id > 0) {
            $good = db('sck_warehouse_good')
                ->where(['good_id'=>$good_id])->find();
            if($good['good_number']>$good['good_warn']){
                return  $this->error('该商品库存未到预警值，不可入库！');
            }
            $GoodLogModel = new WarehouseGoodLogModel();
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['good_name', 'require', '商品名称不能为空'],
                    ['good_price', 'require', '商品单价不能为空'],
                    ['good_total', 'require', '商品总价不能为空'],
                    ['good_amount', 'require', '入库数量不能为空'],
                ]);
                $admin_id = Session::get('admin');
                if(!$admin_id){
                    $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                    return $json;
                }
                $post['admin_id'] = $admin_id;
                $post['good_status'] = 1;
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                Db::startTrans();
                try {
                    $ok = $GoodLogModel->allowField(true)->save($post);
                    if($ok){
                        $insert = db('sck_warehouse_good')
                            ->where(['good_id' => $good_id])
                            ->update(['good_number' => WarehouseGoodModel::raw('good_number+'.$post['good_amount'].''), 'good_amount' => WarehouseGoodModel::raw('good_amount+'.$post['good_amount'].'')]);
                        if($insert){
                            addlog($good_id);
                            $json = ['code'=>1,'msg'=>'入库成功！','url'=>''];
                        }else{
                            throw new \Exception('入库失败，请重试!');
                        }
                    }else{
                        throw new \Exception('入库失败，请重试!');
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
                $this->assign('good_id', $good_id);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    public function GoodOut()
    {
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        if($good_id > 0) {
            $good = db('sck_warehouse_good')
                ->where(['good_id'=>$good_id])->find();
            if(empty($good) or $good['good_number']<=0){
                return  $this->error('该商品库存不足，不可出库，请联系管理员进行整理库存！');
            }
            $GoodLogModel = new WarehouseGoodLogModel();
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['good_price', 'require', '最低出库价不能为空'],
                    ['good_amount', 'require', '出库数量不能为空'],
                ]);

                $admin_id = Session::get('admin');
                if(!$admin_id){
                    $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                    return $json;
                }
                $post['admin_id'] = $admin_id;
                $post['good_status'] = 2;
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if($post['good_price']<$post['lowest_price']){
                    return  $this->error('该商品最低出库价'.$post['lowest_price'].',不足以出库！');
                }
                if($good['good_number']<$post['good_amount']){
                    return  $this->error('该商品库存剩余'.$good['good_number'].',不足以出库！');
                }
                Db::startTrans();
                try {
                    $ok = $GoodLogModel->allowField(true)->save($post);
                    if($ok){
                        $insert = db('sck_warehouse_good')
                            ->where(['good_id' => $good_id])
                            ->update(['good_number' => WarehouseGoodModel::raw('good_number-'.$post['good_amount'].'')]);
                        if($insert){
                            addlog($good_id);
                            $json = ['code'=>1,'msg'=>'出库成功！','url'=>''];
                        }else{
                            throw new \Exception('出库失败，请重试!');
                        }
                    }else{
                        throw new \Exception('出库失败，请重试!');
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
                $good_log = db('sck_warehouse_good_log')
                    ->where(['good_id'=>$good_id,'good_status'=>1])
                    ->order('create_time','desc')
                    ->limit(2)
                    ->select();
                if(empty($good_log)){
                    return $this->error('该商品暂无可用库存，请联系管理员，确认库存！');
                }else{
                    $total_price = array_sum(array_column($good_log,'good_total'));
                    $total_amount = array_sum(array_column($good_log,'good_amount'));
                    $lowest_price = $total_price/$total_amount;
                }
                $ClientModel = new SckClient();
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
                if(!empty($address_ids)){
                    $street_ids = db('address')
                        ->where(['id1'=>1,'status'=>1,'id4'=>['in',$address_ids]])
                        ->column('id5');
                    $street_ids = array_filter($street_ids);
                }else{
                    $street_ids =[];
                }
                $Client = $ClientModel
                    ->where(['client_position_id' => ['in',$street_ids]])
                    ->order('create_time desc')
                    ->select();
                $this->assign('lowest_price', $lowest_price);
                $this->assign('good_id', $good_id);
                $this->assign('good', $good);
                $this->assign('client', $Client);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    public function GoodReturn()
    {
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        if($good_id > 0) {

            $GoodLogModel = new WarehouseGoodLogModel();
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['good_name', 'require', '商品名称不能为空'],
//                    ['good_price', 'require', '商品单价不能为空'],
//                    ['good_total', 'require', '商品总价不能为空'],
                    ['good_amount', 'require', '入库数量不能为空'],
                ]);
                $admin_id = Session::get('admin');
                if(!$admin_id){
                    $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                    return $json;
                }
                $post['admin_id'] = $admin_id;
                $post['good_status'] = 3;
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                Db::startTrans();
                try {
                    $ok = $GoodLogModel->allowField(true)->save($post);
                    if($ok){
                        $insert = db('sck_warehouse_good')
                            ->where(['good_id' => $good_id])
                            ->update(['good_number' => WarehouseGoodModel::raw('good_number+'.$post['good_amount'].'')]);
                        if($insert){
                            addlog($good_id);
                            $json = ['code'=>1,'msg'=>'退货入库成功！','url'=>''];
                        }else{
                            throw new \Exception('退货入库失败，请重试!');
                        }
                    }else{
                        throw new \Exception('退货入库失败，请重试!');
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
                $ClientModel = new SckClient();
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
                if(!empty($address_ids)){
                    $street_ids = db('address')
                        ->where(['id1'=>1,'status'=>1,'id4'=>['in',$address_ids]])
                        ->column('id5');
                    $street_ids = array_filter($street_ids);
                }else{
                    $street_ids =[];
                }
                $Client = $ClientModel
                    ->where(['client_position_id' => ['in',$street_ids]])
                    ->order('create_time desc')
                    ->select();
                $this->assign('client', $Client);
                $this->assign('good_id', $good_id);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    public function GoodLog()
    {
        $post = $this->request->param();
        $good_status = $this->request->has('good_status') ? $this->request->param('good_status', 0, 'intval') : 0;
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['good_name'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }
        $admin_id = Session::get('admin');
        if (isset($admin_id) and $admin_id !== 1) {
            $where['admin_id'] = $admin_id;
        }
        if (isset($good_id) and $good_id !== 0) {
            $where['good_id'] = $good_id;
            $this->assign('good_id', $good_id);
        }
        $model = new WarehouseGoodLogModel();
        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            : $model->where($where)
                ->order('create_time desc')
                ->paginate(20, false, ['query' => $this->request->param()]);

        $this->assign('data', $data);
        $this->assign('good_status', $good_status);
        return $this->fetch();
    }

}
