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

use app\admin\controller\Permissions;
use app\admin\model\User as usermodel;
use think\Db;
use \think\Controller;
use \think\Session;

class User extends Permissions
{
    public function index()
    {

        $goods = Db::name('user_home')->paginate(5);

        $this->assign('goods', $goods);

        return $this->fetch();
    }

    public function publish()
    {
        //获取菜单id
        $id    = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new usermodel();

        //是正常添加操作
        //是修改操作
        if ($this->request->isPost()) {
            //是提交操作
            $post = $this->request->post();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['name', 'require', '姓名不能为空'],
                ['pro', 'require', '请填写省份'],
                ['tel', 'require', '请填写手机号'],
            ]);
            //验证部分数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            //验证菜单是否存在
            $article = $model->where('id', $id)->find();
            if (empty($article)) {
                return $this->error('id不正确');
            }
            //设置修改人
            $post['edit_admin_id'] = Session::get('admin');
            if (false == $model->allowField(true)->save($post, ['id' => $id])) {
                return $this->error('修改失败');
            } else {
                // addlog($model->id);//写入日志
                return $this->success('修改成功', 'admin/user/index');
            }
        } else {
            //非提交操作
            $article = $model->where('id', $id)->find();
            if (!empty($article)) {
                $this->assign('goods', $article);
                return $this->fetch();
            } else {
                return $this->error('id不正确');
            }
        }

    }

    public function delete()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if (false == Db::name('user_home')->where('id', $id)->delete()) {
                return $this->error('删除失败');
            } else {
                // addlog($id);//写入日志
                return $this->success('删除成功', 'admin/user/index');
            }
        }
    }

    public function isTop()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (false == Db::name('user_home')->where('id', $post['id'])->update(['is_top' => $post['is_top']])) {
                return $this->error('设置失败');
            } else {
                // addlog($post['id']);//写入日志
                return $this->success('设置成功', 'admin/goods/index');
            }
        }
    }

    public function status()
    {

        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (false == Db::name('user_home')->where('id', $post['id'])->update(['status' => $post['status']])) {
                return $this->error('设置失败');

            } else {

                // addlog($post['id']);//写入日志
                return $this->success('设置成功', 'admin/user/index');
            }
        }
    }

    public function addimg() // 添加图片

    {
        // echo '这dwqdwqd';
        $id   = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0; // 获取商品id
        $shop = Db::name('');
        // echo $id;

        return view('goods/addimg');
    }

    public function img()
    {
        return '提交图片方法';
    }

    public function del() // 删除方法

    {
        return '删除图片';
    }
}
