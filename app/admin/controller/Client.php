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
use app\admin\model\SckClient as ClientModel;

class Client extends Permissions
{
    public function index()
    {
        $model = new ClientModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['nickname'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['admin_cate_id']) and $post['admin_cate_id'] > 0) {
            $where['admin_cate_id'] = $post['admin_cate_id'];
        }
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }
        $data = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20,false,['query'=>$this->request->param()]);

//        $admin_cate = Db::name('admin_cate')->select();
//        $this->assign('admin_cate',$admin_cate);

        $this->assign('data',$data);
        return $this->fetch();
    }


    public function publish()
    {
        $client_id = $this->request->has('client_id') ? $this->request->param('client_id', 0, 'intval') : 0;
        $model = new ClientModel();
        if($client_id > 0) {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate([
                    ['client_name', 'require', '客户姓名不能为空'],
                    ['client_phone', 'require', '客户电话不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $name = $model->where(['client_phone'=>$post['client_phone'],'client_id'=>['neq',$post['client_id']]])->select();
                if(!empty($name)) {
                    return $this->error('提交失败：该客户手机已被添加');
                }
                if(false == $model->allowField(true)->save($post,['client_id'=>$client_id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('修改管理员信息成功','admin/client/index');
                }
            } else {
                $data = $model->where('client_id',$client_id)->find();
                $this->assign('data',$data);
                return $this->fetch();
            }
        } else {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new \think\Validate([
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
                    return $this->error('添加管理员失败');
                } else {
                    addlog($model->client_id);
                    return $this->success('添加管理员成功','admin/client/index');
                }
            } else {
                return $this->fetch();
            }
        }
    }

    /**
     * 客户删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
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


    public function orders()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $i = 0;
            foreach ($post['id'] as $k => $val) {
                $order = Db::name('admin_menu')->where('id',$val)->value('orders');
                if($order != $post['orders'][$k]) {
                    if(false == Db::name('admin_menu')->where('id',$val)->update(['orders'=>$post['orders'][$k]])) {
                        return $this->error('更新失败');
                    } else {
                        $i++;
                    }
                }
            }
            addlog();//写入日志
            return $this->success('成功更新'.$i.'个数据','admin/menu/index');
        }
    }
}
