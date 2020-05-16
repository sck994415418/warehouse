<?php

namespace app\admin\model;

use \think\Model;
class AdminLog extends Model
{
	public function admin()
    {
        //关联管理员表
        return $this->belongsTo('Admin');
    }


    public function menu()
    {
        //关联菜单表
        return $this->belongsTo('AdminMenu');
    }
}
