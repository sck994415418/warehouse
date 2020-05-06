<?php
namespace app\index\controller;

use app\admin\controller\Permissions;
use think\Db;
use \think\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->error('正在开发中');
        return $this->fetch();
    }

}
