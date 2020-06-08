<?php


namespace app\admin\controller;

use app\admin\model\AdminCate;
use app\admin\model\AdminLog;
use app\index\model\UserModel;
use \think\Db;
use \think\Cookie;
use \think\Session;
use app\admin\model\Admin as adminModel;//管理员模型
use app\admin\model\AdminMenu;
use app\admin\controller\Permissions;
use think\Validate;

class Admin extends Permissions
{
    /**
     * 管理员列表
     * @return [type] [description]
     */
    public function index()
    {

        //实例化管理员模型
        $model = new adminModel();
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

        $admin = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20,false,['query'=>$this->request->param()]);

        $this->assign('admin',$admin);
        $info['cate'] = Db::name('admin_cate')->select();
        $this->assign('info',$info);
        return $this->fetch();
    }


    /**
     * 管理员个人资料修改，属于无权限操作，仅能修改昵称和头像，后续可增加其他字段
     * @return [type] [description]
     */
    public function personal()
    {
        //获取管理员id
        $id = Session::get('admin');
        $model = new adminModel();
        if($id > 0) {
            //是修改操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证昵称是否存在
                $nickname = $model->where(['nickname'=>$post['nickname'],'id'=>['neq',$post['id']]])->select();
                if(!empty($nickname)) {
                    return $this->error('提交失败：该昵称已被占用');
                }
                if(false == $model->allowField(true)->save($post,['id'=>$id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->id);//写入日志
                    return $this->success('修改个人信息成功','admin/admin/personal');
                }
            } else {
                //非提交操作
                $info['admin'] = $model->where('id',$id)->find();
                $this->assign('info',$info);
                return $this->fetch();
            }
        } else {
            return $this->error('id不正确');
        }
    }


    /**
     * 管理员的添加及修改
     * @return [type] [description]
     */
    public function publish()
    {
    	$id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
    	$model = new adminModel();
        $category =  db('sck_warehouse_good_category')->field('category_id as id,category_name as name,parent_id')->select();

    	if($id > 0) {
    		//是修改操作
    		if($this->request->isPost()) {
    			//是提交操作
    			$post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new Validate([
	                ['name', 'require|alphaDash', '管理员名称不能为空|用户名格式只能是字母、数组、——或_'],
	                ['admin_cate_id', 'require', '请选择管理员分组'],
	            ]);
//                dump($post);die;
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                //验证用户名是否存在
                $name = $model->where(['name'=>$post['name'],'id'=>['neq',$post['id']]])->select();
                if(!empty($name)) {
                    return $this->error('提交失败：该用户名已被注册');
                }
                if(!empty($post['address_ids'])) {
                    $post['address_ids'] = json_encode($post['address_ids']);
                }else{
                    $post['address_ids'] = '';
                }
                if(!empty($post['admin_supplier_ids'])) {
                    $post['admin_supplier_ids'] = json_encode($post['admin_supplier_ids']);
                }else{
                    $post['admin_supplier_ids'] = '';
                }
                //验证昵称是否存在
	            $nickname = $model->where(['nickname'=>$post['nickname'],'id'=>['neq',$post['id']]])->select();
	            if(!empty($nickname)) {
	            	return $this->error('提交失败：该昵称已被占用');
	            }
//	            dump($post);die;
	            if(false == $model->allowField(true)->save($post,['id'=>$id])) {
	            	return $this->error('修改失败');
	            } else {
                    addlog($model->id);//写入日志
	            	return $this->success('修改管理员信息成功','admin/admin/index');
	            }
    		} else {
    			//非提交操作
    			$info['admin'] = $model->where('id',$id)->find();
    			$info['admin_cate'] = Db::name('admin_cate')->select();
                if(!empty($id)){
                    $user_info = adminModel::get($id);
                    if(!empty($user_info->address_ids)){
                        $address_ids = json_decode($user_info->address_ids,true);
                        $address = address_fun($address_ids);
//                        $my_address = db('address')
//                            ->where('id4','IN',$address_ids)
//                            ->column('id4 as id,name4 as title,field,true as checked,name2,name3');
////                        dump($my_address);die;
//                        $this->assign('my_address',$my_address);
                    }else{
                        $address = address_fun();
                    }
                }
//                dump($address);die;
                $this->assign('address',json_encode($address));
//                $this->assign('address',$address);
                $category = getrole($category);
                if(!empty($info['admin']['admin_supplier_ids'])){
                    $info['admin']['admin_supplier_ids'] = json_decode($info['admin']['admin_supplier_ids'],true);
                    if(!empty($category)){
                        foreach ($category as $ks=>$vs){
                            $category[$ks]['open'] = 'false';
                            if(!empty($category[$ks]['children'])){
                                foreach ($category[$ks]['children'] as $kss=>$vss){
                                    $category[$ks]['children'][$kss]['open'] = 'false';
                                    if(!empty($category[$ks]['children'][$kss]['children'])){
                                        foreach ($category[$ks]['children'][$kss]['children'] as $ksss=>$vsss){
                                            $category[$ks]['children'][$kss]['children'][$ksss]['field'] = 'admin_supplier_ids[]';
                                            $category[$ks]['children'][$kss]['children'][$ksss]['open'] = false;
                                            if(in_array($category[$ks]['children'][$kss]['children'][$ksss]['id'],$info['admin']['admin_supplier_ids'])){
                                                $category[$ks]['children'][$kss]['children'][$ksss]['checked'] = true;
                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }
                }else{
                    if(!empty($category)){
                        foreach ($category as $ks=>$vs){
                            $category[$ks]['open'] = 'false';
                            $category[$ks]['ico'] = '';
                            if(!empty($category[$ks]['children'])){

                                foreach ($category[$ks]['children'] as $kss=>$vss){
                                    $category[$ks]['children'][$kss]['ico'] = '';
                                    $category[$ks]['children'][$kss]['open'] = 'false';
                                    if(!empty($category[$ks]['children'][$kss]['children'])){
                                        foreach ($category[$ks]['children'][$kss]['children'] as $ksss=>$vsss){
                                            $category[$ks]['children'][$kss]['children'][$ksss]['open'] = 'false';
                                            $category[$ks]['children'][$kss]['children'][$ksss]['ico'] = '';
                                            $category[$ks]['children'][$kss]['children'][$ksss]['field'] = 'admin_supplier_ids[]';

                                        }
                                    }

                                }
                            }

                        }
                    }
                }
                $this->assign('category',json_encode($category));
    			$this->assign('info',$info);
    			return $this->fetch();
    		}
    	} else {
    		//是新增操作
            $address = address_fun();
//    	dump($address);die;
            $this->assign('address',json_encode($address));
//            $this->assign('address',$address);
    		if($this->request->isPost()) {
    			//是提交操作
    			$post = $this->request->post();
    			//验证  唯一规则： 表名，字段名，排除主键值，主键名
	            $validate = new Validate([
	                ['name', 'require|alphaDash', '用户名不能为空|用户名格式只能是字母、数组、——或_'],
	                ['password', 'require|confirm', '密码不能为空|两次密码不一致'],
	                ['password_confirm', 'require', '重复密码不能为空'],
	                ['admin_cate_id', 'require', '请选择管理员分组'],
	                ['thumb', 'require', '请上传头像'],
	            ]);
	            //验证部分数据合法性
	            if (!$validate->check($post)) {
	                $this->error('提交失败：' . $validate->getError());
	            }
	            //验证用户名是否存在
	            $name = $model->where('name',$post['name'])->select();
	            if(!empty($name)) {
	            	return $this->error('提交失败：该用户名已被注册');
	            }
	            if(!empty($post['address_ids'])) {
	                $post['address_ids'] = json_encode($post['address_ids']);
	            }

	            //验证昵称是否存在
	            $nickname = $model->where('nickname',$post['nickname'])->select();
	            if(!empty($nickname)) {
	            	return $this->error('提交失败：该昵称已被占用');
	            }
	            //密码处理
	            $post['password'] = password($post['password']);
	            if(false == $model->allowField(true)->save($post)) {
	            	return $this->error('添加管理员失败');
	            } else {
                    addlog($model->id);//写入日志
	            	return $this->success('添加管理员成功','admin/admin/index');
	            }
    		} else {
    			//非提交操作
    			$info['admin_cate'] = Db::name('admin_cate')->select();
                $category = getrole($category);
    			$this->assign('info',$info);
    			$this->assign('category',json_encode($category));

                return $this->fetch();
    		}
    	}
    }

    /**
     * 修改密码
     * @return [type] [description]
     */
    public function editPassword()
    {
    	$id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
    	if($id > 0) {
    		if($id == Session::get('admin')) {
    			$post = $this->request->post();
    			//验证  唯一规则： 表名，字段名，排除主键值，主键名
	            $validate = new Validate([
	                ['password', 'require|confirm', '密码不能为空|两次密码不一致'],
	                ['password_confirm', 'require', '重复密码不能为空'],
	            ]);
	            //验证部分数据合法性
	            if (!$validate->check($post)) {
	                $this->error('提交失败：' . $validate->getError());
	            }
    			$admin = Db::name('admin')->where('id',$id)->find();
    			if(password($post['password_old']) == $admin['password']) {
    				if(false == Db::name('admin')->where('id',$id)->update(['password'=>password($post['password'])])) {
    					return $this->error('修改失败');
    				} else {
                        addlog();//写入日志
    					return $this->success('修改成功','admin/main/index');
    				}
    			} else {
    				return $this->error('原密码错误');
    			}
    		} else {
    			return $this->error('不能修改别人的密码');
    		}
    	} else {
            $id = Session::get('admin');
            $this->assign('id',$id);
    		return $this->fetch();
    	}
    }


    /**
     * 管理员删除
     * @return [type] [description]
     */
    public function delete()
    {
    	if($this->request->isAjax()) {
    		$id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
    		if($id == 1) {
    			return $this->error('网站所有者不能被删除');
    		}
    		if($id == Session::get('admin')) {
    			return $this->error('自己不能删除自己');
    		}
    		if(false == Db::name('admin')->where('id',$id)->delete()) {
    			return $this->error('删除失败');
    		} else {
                addlog($id);//写入日志
    			return $this->success('删除成功','admin/admin/index');
    		}
    	}
    }


    /**
     * 管理员权限分组列表
     * @return [type] [description]
     */
    public function adminCate()
    {
    	$model = new AdminCate;

        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['name'] = ['like', '%' . $post['keywords'] . '%'];
        }

        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

        $cate = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20,false,['query'=>$this->request->param()]);

    	$this->assign('cate',$cate);
    	return $this->fetch();

    }


    /**
     * 管理员角色添加和修改操作
     * @return [type] [description]
     */
    public function adminCatePublish()
    {
        //获取角色id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new AdminCate();
        $menuModel = new AdminMenu();
        if($id > 0) {
            //是修改操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new Validate([
                    ['name', 'require', '角色名称不能为空'],
                ]);
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                //验证用户名是否存在
                $name = $model->where(['name'=>$post['name'],'id'=>['neq',$post['id']]])->select();
                if(!empty($name)) {
                    return $this->error('提交失败：该角色名已存在');
                }
                //处理选中的权限菜单id，转为字符串
                if(!empty($post['admin_menu_id'])) {
                    $post['permissions'] = implode(',',$post['admin_menu_id']);
                } else {
                    $post['permissions'] = '0';
                }
                if(false == $model->allowField(true)->save($post,['id'=>$id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->id);//写入日志
                    return $this->success('修改角色信息成功','admin/admin/adminCate');
                }
            } else {
                //非提交操作
                $info['cate'] = $model->where('id',$id)->find();
                if(!empty($info['cate']['permissions'])) {
                    //将菜单id字符串拆分成数组
                    $info['cate']['permissions'] = explode(',',$info['cate']['permissions']);
                }
                $menus = Db::name('admin_menu')->select();
                $info['menu'] = $this->menulist($menus);
                $this->assign('info',$info);
                return $this->fetch();
            }
        } else {
            //是新增操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new Validate([
                    ['name', 'require', '角色名称不能为空'],
                ]);
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                //验证用户名是否存在
                $name = $model->where('name',$post['name'])->find();
                if(!empty($name)) {
                    return $this->error('提交失败：该角色名已存在');
                }
                //处理选中的权限菜单id，转为字符串
                if(!empty($post['admin_menu_id'])) {
                    $post['permissions'] = implode(',',$post['admin_menu_id']);
                }
                if(false == $model->allowField(true)->save($post)) {
                    return $this->error('添加角色失败');
                } else {
                    addlog($model->id);//写入日志
                    return $this->success('添加角色成功','admin/admin/adminCate');
                }
            } else {
                //非提交操作
                $menus = Db::name('admin_menu')->select();
                $info['menu'] = $this->menulist($menus);
                //$info['menu'] = $this->menulist($info['menu']);
                $this->assign('info',$info);
                return $this->fetch();
            }
        }
    }


    public function preview()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new AdminCate();
        $info['cate'] = $model->where('id',$id)->find();
        if(!empty($info['cate']['permissions'])) {
            //将菜单id字符串拆分成数组
            $info['cate']['permissions'] = explode(',',$info['cate']['permissions']);
        }
        $menus = Db::name('admin_menu')->select();
        $info['menu'] = $this->menulist($menus);
        $this->assign('info',$info);
        return $this->fetch();
    }


    protected function menulist($menu,$id=0,$level=0){

        static $menus = array();
        $size = count($menus)-1;
        foreach ($menu as $value) {
            if ($value['pid']==$id) {
                $value['level'] = $level+1;
                if($level == 0)
                {
                    $value['str'] = str_repeat('',$value['level']);
                    $menus[] = $value;
                }
                elseif($level == 2)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }
                elseif($level == 3)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }
                else
                {
                    $value['str'] = '&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }

                $this->menulist($menu,$value['id'],$value['level']);
            }
        }
        return $menus;
    }


    /**
     * 管理员角色删除
     * @return [type] [description]
     */
    public function adminCateDelete()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if($id > 0) {
                if($id == 1) {
                    return $this->error('超级管理员角色不能删除');
                }
                if(false == Db::name('admin_cate')->where('id',$id)->delete()) {
                    return $this->error('删除失败');
                } else {
                    addlog($id);//写入日志
                    return $this->success('删除成功','admin/admin/adminCate');
                }
            } else {
                return $this->error('id不正确');
            }
        }
    }


    public function log()
    {
        $model = new AdminLog();

        $post = $this->request->param();
        if (isset($post['admin_menu_id']) and $post['admin_menu_id'] > 0) {
            $this->assign('admin_menu_id',$post['admin_menu_id']);
            $where['admin_menu_id'] = $post['admin_menu_id'];
        }

        if (isset($post['admin_id']) and $post['admin_id'] > 0) {
            $this->assign('admin_id',$post['admin_id']);
            $where['admin_id'] = $post['admin_id'];
        }

        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
            $this->assign('create_time',$post['create_time']);
        }

        $log = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20,false,['query'=>$this->request->param()]);

        $this->assign('log',$log);
        //身份列表
        $admin_cate = Db::name('admin_cate')->select();
        $this->assign('admin_cate',$admin_cate);
        $info['menu'] = Db::name('admin_menu')->where('type',1)->select();
        $info['admin'] = Db::name('admin')->select();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function status_on()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0) : 0;
//            dump($id);die;
            if($id>0) {
                $model = new adminModel();
                if(false == $model->save(['admin_status'=>1],['id'=>$id])) {
                    return $this->error('开启失败');
                } else {
                    addlog($id);
                    return $this->success('开启成功');
                }
            } else {
                return $this->error('页面错误，请刷新后重试！');
            }
        }
    }
    public function status_off()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0) : 0;
//            dump($id);die;
            if($id>0) {
                $model = new adminModel();
                if(false == $model->save(['admin_status'=>0],['id'=>$id])) {
                    return $this->error('禁用失败');
                } else {
                    addlog($id);
                    return $this->success('禁用成功');
                }
            } else {
                return $this->error('页面错误，请刷新后重试！');
            }
        }
    }
}
