<?php
namespace app\admin\controller;

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
//        dump($post);
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['client_name|client_phone|client_wechat'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['client_position_id']) and $post['client_position_id'] > 0) {
            $new_client_position_id = substr($post['client_position_id'],0,$post['num']);
            $where['client_position_id'] = ['like',$new_client_position_id.'%'];
        }
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

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
        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            : $model->where($where)
                ->where(['client_position_id' => ['in',$street_ids]])
                ->order('create_time desc')
                ->paginate(20,false,['query'=>$this->request->param()]);

        $address = db('address')
            ->field('id1,id2 as address_id, name2 as address_name')
            ->where(['id1'=>1,'status'=>1,'id4'=>['in',$address_ids]])
            ->group('address_id')
            ->select();


        $this->assign('View_address',$address);
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function publish()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $model = new ClientModel();

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

        if($client_id > 0) {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
//                    ['client_name', 'require', '客户姓名不能为空'],
//                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                if(!empty($post['client_phone'])) {
                    $name = $model->where(['client_phone' => $post['client_phone'], 'client_id' => ['neq', $post['client_id']]])->select();
                }
                if(!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                if(false == $model->allowField(true)->save($post,['client_id'=>$client_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('修改信息成功','admin/client/index');
                }
            } else {
                $data = $model->where('client_id',$client_id)->find();
                $this->assign('data',$data);
                return $this->fetch();
            }
        } else {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['client_name', 'require', '客户姓名不能为空'],
                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $name = $model->where(['client_phone'=>$post['client_phone']])->select();
                if(!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                $post['create_time'] = time();
                if(false == $model->allowField(true)->save($post)) {
                    return $this->error('添加失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('添加成功','admin/client/index');
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
            $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
            $model = new ClientModel();
            if(false == $model->where('client_id',$client_id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($client_id);//写入日志
                return $this->success('删除成功','admin/client/index');
            }
        }
    }

}
