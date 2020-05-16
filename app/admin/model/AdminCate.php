<?php

namespace app\admin\model;

use \think\Model;
class AdminCate extends Model
{
	public function admin()
    {
        //关联管理员表
        return $this->hasOne('Admin');
    }
}
