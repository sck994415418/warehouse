<?php


namespace app\admin\model;

use \think\Model;
class SckWarehouseGoodLog extends Model
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
    public function goodlogpay()
    {
        return $this->belongsTo('SckWarehouseGoodLogPay','log_id','log_id');
    }
    public function nickname()
    {
        //关联管理员表
        return $this->belongsTo('Admin','admin_id','id');
    }
}
