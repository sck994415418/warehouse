<?php

namespace app\admin\controller;

use app\admin\model\SckClient;
use app\admin\model\SckWarehouseGoodLog;
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
                $ok = $GoodLogPayModel->allowField(true)->save(['pay_price'=>$post['pay_price']],['log_id'=>$post['log_id'],'pay_status'=>$post['pay_status']]);
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

    public function pay_order()
    {
        if(request()->isPost()) {
            $data = request()->post();
            $error_arr = [];
            $log_desc = [];
            $success_arr = 0;
            $success_price = 0;
            $client_id = 0;
            if(!empty($data)){
                $data = $data['data'];
                foreach ($data as $k=>$v){
                    if(isset($data[$k]['pay_price_new']) and is_numeric($data[$k]['pay_price_new'])){
                        $pay_price_new_total = $data[$k]['pay_price_new']+$data[$k]['pay_price'];
                        if($pay_price_new_total>$data[$k]['pay_total']){
                            $error_arr[] = $data[$k]['pay_id'];
                        }else{
                            $res = db('sck_warehouse_good_log_pay')->where(['pay_id'=>$data[$k]['pay_id']])->update(['pay_price' =>WarehouseGoodLogPayModel::raw('pay_price+' . intval($data[$k]['pay_price_new'] . ''))]);
                            if($res){
                                $log_desc[] = [
                                    'log_id' => $data[$k]['pay_id']
                                    ,'pay_price' => $data[$k]['pay_price_new']];
                                $success_price += $data[$k]['pay_price_new'];
                                $client_id = $data[$k]['client_id'];
                                $success_arr++;
                            }
                        }
                    }

                }
                if(!empty($error_arr)){
                    $error_arr = implode($error_arr,',');
                    $error_arr = 'ID为['.$error_arr.']的记录付款金额有误，请检查！';
                }else{
                    $error_arr = '';
                }
                $admin_id = Session::get('admin');
                if (!$admin_id) {
                    $json = ['code' => 0, 'msg' => '页面错误，刷新后重试！', 'url' => ''];
                    return $json;
                }
                $post['admin_id'] = $admin_id;
                $log_desc = json_encode($log_desc);
//                dump($log_desc);die;
                db('sck_client_paylog')->insert(['client_id'=>$client_id,'admin_id'=>$admin_id,'create_time'=>time(),'pay_price'=>$success_price,'log_desc'=>$log_desc]);
                $this->success($success_arr.'条记录付款成功!<br><br>'.$error_arr);
//                dump($data);die;
            }else{
                return $this->error('数据错误，请重试！');
            }
        }elseif(request()->isGet()){
            $ClientModel = new SckClient();
            $arr = $this->request->has('data') ? $this->request->param('data') : 0;
            $this->assign('arr',$arr);
            $Client = $ClientModel
//            ->where(['client_position_id' => ['in', $street_ids]])
                ->order('create_time desc')
                ->select();
            $this->assign('client',$Client);
            return $this->fetch();
        }
    }
    public function pay_order_list()
    {
        $arr = $this->request->has('data') ? $this->request->param('data') : 0;
        $input = request()->get();
        $where['swgl.log_id'] = ['in',$arr];
        if (isset($input['good_name']) and !empty($input['good_name'])) {
            $where['swg.good_name'] = ['like', '%' . $input['good_name'] . '%'];
        }
        if (isset($input['page']) and !empty($input['page'])) {
            $page = $input['page'];
        }else{
            $page = 1;
        }
        if (isset($input['limit']) and !empty($input['limit'])) {
            $number = $input['limit'];
        }else{
            $number = 20;
        }

        if (isset($input['time']) and !empty($input['time'])) {
            $start_time = strtotime(substr($input['time'], 0, strripos($input['time'], ' - ')));
            $end_time = strtotime(substr($input['time'], strripos($input['time'], ' - ') + 3));
            $where['swgl.create_time'] = ['between', [$start_time, $end_time]];
        }
        if (!empty($input['client_id']) && isset($input['client_id'])) {
            $where['swgl.client_id'] = $input['client_id'];
        }
        $where['swgl.good_status'] = ['in',[2,3]];
        $where['swgp.pay_status'] = ['in',[2,3]];
//        $where['swgl.is_return'] = 0;
        $model = new SckWarehouseGoodLog();
        $data = $model
            ->alias('swgl')
            ->where(@$where)
            ->join('sck_warehouse_good swg', 'swg.good_id = swgl.good_id', 'LEFT')
            ->join('sck_warehouse_good_log_pay swgp', 'swgp.log_id = swgl.log_id', 'LEFT')
            ->join('sck_client sc', 'sc.client_id = swgl.client_id')
            ->field('swgl.*,swgp.pay_id,swgp.is_tax,swg.good_name,swg.good_price as goods_price,swg.good_total as goods_total,swg.tax_status,swg.good_brand,swgp.pay_price,swgp.pay_total,sc.client_name,sc.client_company')
            ->order('create_time desc')
            ->page($page,$number)->select();
        $data_count = $model
            ->alias('swgl')
            ->where(@$where)
            ->join('sck_warehouse_good swg', 'swg.good_id = swgl.good_id', 'LEFT')
            ->join('sck_warehouse_good_log_pay swgp', 'swgp.log_id = swgl.log_id', 'LEFT')
            ->join('sck_client sc', 'sc.client_id = swgl.client_id')
            ->field('swgl.*,swgp.pay_id,swgp.is_tax,swg.good_name,swg.good_price as goods_price,swg.good_total as goods_total,swg.tax_status,swg.good_brand,swgp.pay_price,swgp.pay_total,sc.client_name,sc.client_company')
            ->count();
        if(!empty($data)){
            foreach ($data as $k=>$v){
                $data[$k]['nickname'] = $data[$k]->admin->nickname;
                if($data[$k]['good_status']==3){
                    $data[$k]['pay_price'] = round($data[$k]['pay_price'],2);
                    $data[$k]['pay_total'] = round($data[$k]['pay_total'],2);
                }else{
                    $data[$k]['pay_price'] = round($data[$k]['pay_price'],2);
                    $data[$k]['pay_total'] = round($data[$k]['pay_total'],2);
                }
                $data[$k]['pay_price_new'] = $data[$k]['pay_total']-$data[$k]['pay_price'];
                switch ($data[$k]['tax_status']){
                    case '0':
                        $data[$k]['tax_status']='';
                        break;
                    case '1':
                        $data[$k]['tax_status']='专票';
                        break;
                    case '2':
                        $data[$k]['tax_status']='专票1%';
                        break;
                    case '3':
                        $data[$k]['tax_status']='普票';
                        break;
                    case '4':
                        $data[$k]['tax_status']='无票';
                        break;
                    case '5':
                        $data[$k]['tax_status']='专票3%';
                        break;
                    case '6':
                        $data[$k]['tax_status']='专票6%';
                        break;
                    default:null;
                }
            }
        }
        if (isset($input['pay_monry']) && !empty($input['pay_monry'])) {
            if ($input['pay_monry'] == 1) {
                foreach ($data as $k => $v) {
                    if (empty($v->goodlogpay->pay_price)) {
                        $num = 0;
                    } else {
                        $num = $data[$k]->goodlogpay->pay_price;
                    }
                    if ($num >= 1) {
                        unset($data[$k]);
                    }
                }
            } elseif ($input['pay_monry'] == 2) {
                foreach ($data as $k => $v) {
                    if (empty($v->goodlogpay->pay_price)) {
                        $num = 0;
                    } else {
                        $num = $data[$k]->goodlogpay->pay_price;
                    }
                    if ($num <= 0) {
                        unset($data[$k]);
                    }
                }
            }
        }

        $res['code'] = 1;
        $res['count'] = $data_count;
        $res['data'] = $data;
        $res['msg'] = null;
        $res = json($res);
        return $res;
    }
    //是否开票操作
    public function is_tax()
    {
        $type = $this->request->has('type') ? $this->request->param('type', 0, 'intval') : 0;
        if(isset($type) and $type == 1){
            $data = request()->post();
            $success_arr = 0;
            if(!empty($data)) {
                $data = $data['data'];
                foreach ($data as $k => $v) {
                    $res = db('sck_warehouse_good_log_pay')->where(['pay_id'=>$data[$k]['pay_id']])->update(['is_tax'=>1]);
                    if($res){
                       $success_arr++;
                    }
                }
                $this->success($success_arr . '条记录操作成功!');
            }
        }else{
            $is_checked = $this->request->has('is_checked') ? $this->request->param('is_checked', 0, 'intval') : 0;
            $pay_id = $this->request->has('pay_id') ? $this->request->param('pay_id', 0, 'intval') : 0;
            if(!isset($is_checked) || !isset($pay_id) || $pay_id<0 || $is_checked<0){
                return $this->error('数据错误，请重试！');
            }
            $res = db('sck_warehouse_good_log_pay')->where(['pay_id'=>$pay_id])->update(['is_tax'=>$is_checked]);
            if($res){
                addlog($pay_id);
                return $this->success('操作成功');
            }else{
                return $this->error('操作失败');
            }
        }
    }
}
