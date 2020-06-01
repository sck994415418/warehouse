<?php


namespace app\admin\model;

use \think\Model;
class SckWarehouseGoodLogPay extends Model
{
    public function admin()
    {
        //关联管理员表
        return $this->belongsTo('Admin');
    }
    public function sckclient()
    {
        return $this->belongsTo('SckClient');
    }
    public function goodlog()
    {
        return $this->belongsTo('SckWarehouseGoodLog');
    }
}
