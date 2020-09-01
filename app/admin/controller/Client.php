<?php

namespace app\admin\controller;

use app\admin\model\SckClient;
use app\admin\model\SckClientPaylog;
use app\admin\model\SckWarehouseGoodLog as WarehouseGoodLogModel;
use think\Db;
use app\admin\model\SckClient as ClientModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;

class Client extends Permissions
{
    public function index()
    {
        $model = new ClientModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['client_name|client_company|client_phone|client_wechat'] = ['like', '%' . $post['keywords'] . '%'];
            $this->assign('keywords',$post['keywords']);
        }
        if (isset($post['client_position_id']) and $post['client_position_id'] > 0) {
            $new_client_position_id = substr($post['client_position_id'], 0, $post['num']);
            $where['client_position_id'] = ['like', $new_client_position_id . '%'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }
        if (isset($post['time']) and !empty($post['time'])) {
            $start_time = strtotime(substr($post['time'], 0, strripos($post['time'], ' - ')));
            $end_time = strtotime(substr($post['time'], strripos($post['time'], ' - ') + 3));
            $where['create_time'] = ['between', [$start_time, $end_time]];
        }
        //================================================================================================
//        $id = Session::get('admin');
//        if (!empty($id)) {
//            $user_info = \app\admin\model\Admin::get($id);
//            if (!empty($user_info->address_ids)) {
//                $address_ids = json_decode($user_info->address_ids, true);
//                if (!empty($address_ids)) {
//                    $address_ids = db('address')->where(['status' => 1, 'id4' => ['in', $address_ids]])->column('id4');
//                } else {
//                    $address_ids = [];
//                }
//            } else {
//                $address_ids = [];
//            }
//        } else {
//            $address_ids = [];
//        }
//        if (!empty($address_ids)) {
//            $street_ids = db('address')
//                ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
//                ->column('id4');
//            $street_ids = array_filter($street_ids);
//
//        } else {
//            $street_ids = [];
//        }
//        $position = $model->column('client_position_id');
//        $result = array_intersect($position,$street_ids);
        //================================================================================================
        $data = empty($where) ? $model
            //================================================================================================
//            ->where(['client_position_id' => ['in', $result]])
            //================================================================================================
            ->where(['client_type' => 1])
            ->order('create_time desc')
            ->paginate(20)
            ->each(function ($k, $v) {
                $k['client_total'] = db('sck_warehouse_good_log_pay')
                    ->where(['client_id' => $k['client_id'], 'pay_status' =>['in',[2,3]]])
                    ->sum('pay_total');
                db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_total'=>$k['client_total']]);
                $k['client_pay'] = db('sck_warehouse_good_log_pay')
                    ->where(['client_id' => $k['client_id'], 'pay_status' =>['in',[2,3]]])
                    ->sum('pay_price');
                db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_pay'=>$k['client_pay']]);
                $k['client_cost'] = $k['client_total'] - $k['client_pay'];
                db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_cost'=>$k['client_cost']]);

            })
            : $model->where($where)
                //================================================================================================
//                ->where(['client_position_id' => ['in', $result]])
                //================================================================================================
                ->where(['client_type' => 1])
                ->order('create_time desc')
                ->paginate(20, false, ['query' => $this->request->param()])
                ->each(function ($k, $v) {
                    $k['client_total'] = db('sck_warehouse_good_log_pay')
                        ->where(['client_id' => $k['client_id'], 'pay_status' =>['in',[2,3]]])
                        ->sum('pay_total');
                    db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_total'=>$k['client_total']]);
                    $k['client_pay'] = db('sck_warehouse_good_log_pay')
                        ->where(['client_id' => $k['client_id'], 'pay_status' =>['in',[2,3]]])
                        ->sum('pay_price');
                    db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_pay'=>$k['client_pay']]);
                    $k['client_cost'] = $k['client_total'] - $k['client_pay'];
                    db('sck_client')->where(['client_id' => $k['client_id']])->update(['client_cost'=>$k['client_cost']]);
                });

        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            //================================================================================================
//            ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
            //================================================================================================
            ->group('address_id')
            ->select();
        $this->assign('View_address', $address);
        $this->assign('data', $data);

//        dump($data);die;
        return $this->fetch();
    }


    public function publish()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $model = new ClientModel();
        //================================================================================================
//        $id = Session::get('admin');
//        if (!empty($id)) {
//            $user_info = \app\admin\model\Admin::get($id);
//            if (!empty($user_info->address_ids)) {
//                $address_ids = json_decode($user_info->address_ids, true);
//            } else {
//                $address_ids = [];
//            }
//        } else {
//            $address_ids = [];
//        }
        //================================================================================================
        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            //================================================================================================
//            ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
            //================================================================================================
            ->group('address_id')
            ->select();
        $this->assign('View_address', $address);

        if ($client_id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
//                    ['client_name', 'require', '客户姓名不能为空'],
//                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if (!empty($post['client_phone'])) {
                    $name = $model->where(['client_phone' => $post['client_phone'], 'client_id' => ['neq', $post['client_id'], 'client_type' => 1]])->select();
                }
                if (!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                $post['client_type'] = 1;
                if (false == $model->allowField(true)->save($post, ['client_id' => $client_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('修改信息成功');
                }
            } else {
                $data = $model->where('client_id', $client_id)->find();
                $this->assign('data', $data);
                return $this->fetch();
            }
        } else {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['client_name', 'require', '客户姓名不能为空'],
                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $name = $model->where(['client_phone' => $post['client_phone'], 'client_type' => 1])->select();
                if (!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                $post['create_time'] = time();
                $post['client_type'] = 1;
                if (false == $model->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('添加成功');
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
        if ($this->request->isAjax()) {
            $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
            $model = new ClientModel();
            if (false == $model->where('client_id', $client_id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($client_id);//写入日志
                return $this->success('删除成功', 'admin/client/index');
            }
        }
    }

    public function client_details()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $admin_power = $this->request->has('client_power') ? $this->request->param('client_power', 0, 'intval') : 0;
        if (!empty($admin_power) and  $admin_power == 1) {
            return $this->error('您没有权限查看他人用户！');
        }
        if (!empty($client_id)) {
            $model = new SckClient();
            $client = $model->get(['client_id' => $client_id]);
            if (!empty($client)) {
                $this->assign('client', $client);
                return $this->fetch();
            } else {
                return $this->error('未找到该客户！');
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    public function paylog()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $type = $this->request->has('type') ? $this->request->param('type') : null;
        if(!isset($type) || empty($type)){
            if (!empty($client_id)) {
                $model = new SckClient();
                $client = $model->get(['client_id' => $client_id]);
                if (!empty($client)) {
                    $this->assign('client_id',$client_id);
                    return $this->fetch();
                } else {
                    return $this->error('未找到该客户！');
                }
            } else {
                return $this->error('页面错误，请重试！');
            }
        }elseif(isset($type) and $type == 'getlist'){
            $input = request()->get();
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
            $model = new SckClientPaylog();
            $data = $model
                ->where(['client_id' => $client_id])
                ->where(@$where)
                ->order('create_time desc')
                ->page($page,$number)->select();
            $data_count = $model
                ->where(['client_id' => $client_id])
                ->where(@$where)
                ->order('create_time desc')
                ->page($page,$number)->count();
            if(!empty($data)){
                foreach ($data as $k=>$v){
                    $data[$k]['nickname'] = $data[$k]->admin->nickname;
                    $data[$k]['client_name'] = $data[$k]->sckclient->client_name;
                }
            }
//            dump($data);die;
            $res['code'] = 1;
            $res['count'] = $data_count;
            $res['data'] = $data;
            $res['msg'] = null;
            $res = json($res);
            return $res;
        }
        if (!empty($admin_power) and  $admin_power == 1) {
            return $this->error('您没有权限查看他人用户！');
        }

    }

    public function client_details_sck()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        if (!empty($client_id)) {
            $model = new SckClient();
            $client = $model->get(['client_id' => $client_id]);
            if (!empty($client)) {
                $res = json($client);
                return $res;
            } else {
                return $this->error('未找到该客户！');
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

//渠道客户
    public function channl()
    {
        $model = new ClientModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['client_name|client_phone|client_wechat'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['client_position_id']) and $post['client_position_id'] > 0) {
            $new_client_position_id = substr($post['client_position_id'], 0, $post['num']);
            $where['client_position_id'] = ['like', $new_client_position_id . '%'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }
        if (isset($post['time']) and !empty($post['time'])) {
            $start_time = strtotime(substr($post['time'], 0, strripos($post['time'], ' - ')));
            $end_time = strtotime(substr($post['time'], strripos($post['time'], ' - ') + 3));
            $where['create_time'] = ['between', [$start_time, $end_time]];
        }
        //================================================================================================
//        $id = Session::get('admin');
//        if (!empty($id)) {
//            $user_info = \app\admin\model\Admin::get($id);
//            if (!empty($user_info->address_ids)) {
//                $address_ids = json_decode($user_info->address_ids, true);
//                if (!empty($address_ids)) {
//                    $address_ids = db('address')->where(['status' => 1, 'id4' => ['in', $address_ids]])->column('id4');
//                } else {
//                    $address_ids = [];
//                }
//            } else {
//                $address_ids = [];
//            }
//        } else {
//            $address_ids = [];
//        }
//        if (!empty($address_ids)) {
//            $street_ids = db('address')
//                ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
//                ->column('id4');
//            $street_ids = array_filter($street_ids);
//
//        } else {
//            $street_ids = [];
//        }
//        $position = $model->column('client_position_id');
//        $result = array_intersect($position,$street_ids);
        //================================================================================================
        $data = empty($where) ? $model
            //================================================================================================
//            ->where(['client_position_id' => ['in', $result]])
            //================================================================================================
            ->where(['client_type' => 2])
            ->order('create_time desc')
            ->paginate(20)
            ->each(function ($k, $v) {
                $k['client_total'] = db('sck_warehouse_good_log')
                    ->where(['client_id' => $k['client_id'], 'good_status' => 2])
                    ->sum('good_total');
                $k['client_price'] = db('sck_warehouse_good_log_pay')
                    ->where(['client_id' => $k['client_id'], 'pay_status' => 2])
                    ->sum('pay_price');
                $k['good_total_t'] = db('sck_warehouse_good_log')
                    ->where(['client_id' => $k['client_id'], 'good_status' => 3])
                    ->sum('good_total');
                $good_total_t = empty($k['good_total_t']) ? 0 : $k['good_total_t'];
                $k['client_money'] = $k['client_total'] - $good_total_t - $k['client_price'];
            })
            : $model->where($where)
                //================================================================================================
//                ->where(['client_position_id' => ['in', $result]])
                //================================================================================================
                ->where(['client_type' => 2])
                ->order('create_time desc')
                ->paginate(20, false, ['query' => $this->request->param()])
                ->each(function ($k, $v) {
                    $k['client_total'] = db('sck_warehouse_good_log')
                        ->where(['client_id' => $k['client_id'], 'good_status' => 2])
                        ->sum('good_total');
                    $k['client_price'] = db('sck_warehouse_good_log_pay')
                        ->where(['client_id' => $k['client_id'], 'pay_status' => 2])
                        ->sum('pay_price');
                    $k['good_total_t'] = db('sck_warehouse_good_log')
                        ->where(['client_id' => $k['client_id'], 'good_status' => 3])
                        ->sum('good_total');
                    $good_total_t = empty($k['good_total_t']) ? 0 : $k['good_total_t'];
                    $k['client_money'] = $k['client_total'] - $good_total_t - $k['client_price'];
                });

        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            //================================================================================================
//            ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
            //================================================================================================
            ->group('address_id')
            ->select();
        $this->assign('View_address', $address);
        $this->assign('data', $data);
        return $this->fetch();
    }

    //渠道客户添加/修改
    public function ChannlPublish()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $model = new ClientModel();
        //================================================================================================
//        $id = Session::get('admin');
//        if (!empty($id)) {
//            $user_info = \app\admin\model\Admin::get($id);
//            if (!empty($user_info->address_ids)) {
//                $address_ids = json_decode($user_info->address_ids, true);
//            } else {
//                $address_ids = [];
//            }
//        } else {
//            $address_ids = [];
//        }
        //================================================================================================
        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            //================================================================================================
//            ->where(['id1' => 1, 'status' => 1, 'id4' => ['in', $address_ids]])
            //================================================================================================
            ->group('address_id')
            ->select();
        $this->assign('View_address', $address);

        if ($client_id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
//                    ['client_name', 'require', '客户姓名不能为空'],
//                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if (!empty($post['client_phone'])) {
                    $name = $model->where(['client_phone' => $post['client_phone'], 'client_id' => ['neq', $post['client_id']], 'client_type' => 2])->select();
                }
                if (!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                $post['client_type'] = 2;
                if (false == $model->allowField(true)->save($post, ['client_id' => $client_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('修改信息成功');
                }
            } else {
                $data = $model->where('client_id', $client_id)->find();
                $this->assign('data', $data);
                return $this->fetch();
            }
        } else {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['client_name', 'require', '客户姓名不能为空'],
                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $name = $model->where(['client_phone' => $post['client_phone'], 'client_type' => 2])->select();
                if (!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                $post['create_time'] = time();
                $post['client_type'] = 2;
                if (false == $model->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('添加成功');
                }
            } else {
                return $this->fetch();
            }
        }
    }

//    查看客户订单
    public function order()
    {
        $input = request()->get();
        $where['good_status'] = ['in',[2,3]];
//        $where['is_return'] = 0;
        if (isset($input['time']) && !empty($input['time'])) {
            $start_time = strtotime(substr($input['time'], 0, strripos($input['time'], ' - ')));
            $end_time = strtotime(substr($input['time'], strripos($input['time'], ' - ') + 3));
            $where['create_time'] = ['between', [$start_time, $end_time]];
        }
        if (isset($input['client_id']) && !empty($input['client_id'])) {
            $where['client_id'] = $input['client_id'];
            $this->assign('client_id', $input['client_id']);
        }

        if (isset($input['keywords']) and !empty($input['keywords'])) {
            $where['good_name'] = ['like', '%' . $input['keywords'] . '%'];
            $this->assign('search_good_name', $input['keywords']);
        }
        $model = new WarehouseGoodLogModel();
//        $data = $model->where($where)->paginate(20,false,['query' => ['client_id'=>$input['client_id']]]);
        $data = $model->where($where)->order('log_id desc')->select();

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
//        dump($data);die;
        return view('/client/order', ['data' => $data]);
    }

    public function edit($id)
    {
        $model = new SckClient();
        $data = $model->where('client_id', $id)->find();
        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            ->where(['id1' => 1, 'status' => 1])
            ->column('id1,id2 as address_id,name2 as address_name');
        return view('/client/edit', ['data' => $data, 'View_address' => $address]);
    }

}
