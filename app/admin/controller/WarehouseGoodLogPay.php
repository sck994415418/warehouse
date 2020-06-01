<?php

namespace app\admin\controller;

use app\admin\model\SckClient;
use think\Db;
use app\admin\model\SckWarehouseGoodLogPay as WarehouseGoodLogPayModel;
use app\admin\model\SckWarehouseGoodLog as WarehouseGoodLogModel;
use think\Session;
use think\Validate;
use app\admin\model\Admin as adminModel;//管理员模型

class WarehouseGoodLogPay extends Permissions
{
    public function pay_money()
    {
        $log_id = $this->request->has('log_id') ? $this->request->param('log_id', 0, 'intval') : 0;
        if($log_id > 0) {
            $GoodLogPayModel = new WarehouseGoodLogPayModel();
            $GoodLogModel = new WarehouseGoodLogModel();
            $GoodLog = $GoodLogModel->get($log_id);
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['pay_price', 'require', '已付金额不能为空'],
                    ['pay_total', 'require', '应付金额不能为空'],
                ]);
                $admin_id = Session::get('admin');
                if(!$admin_id){
                    $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                    return $json;
                }
                if(empty($post['good_id']) || empty($post['pay_status']) || empty($post['log_id'])){
                    $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                    return $json;
                }
                $post['admin_id'] = $admin_id;
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if($post['pay_total']<$post['pay_price']){
                    $json = ['code'=>0,'msg'=>'已付金额不能大于应付金额！','url'=>''];
                    return $json;
                }
                if(!empty($post['pay_id'])){
                    $ok = $GoodLogPayModel->allowField(true)->save($post,['pay_id'=>$post['pay_id']]);
                }else{
                    $ok = $GoodLogPayModel->allowField(true)->save($post);
                }
                if($ok){
                    addlog($log_id);
                    return $this->success('付款成功！');
                }else{
                    return $this->error('付款失败,请重试！');
                }
            } else {
                $pay_model = new WarehouseGoodLogPayModel();
                $pay = $pay_model->get(['log_id'=>$log_id]);
                $this->assign('pay', @$pay);
                $this->assign('good_log', $GoodLog);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

}
