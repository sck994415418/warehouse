<?php


namespace app\admin\model;

use \think\Model;
class SckWarehouseGood extends Model
{
//    public function category()
//    {
//        return $this->hasOne('SckWarehouseGoodCategory');
//    }
    public function category()
    {
        return $this->belongsTo('SckWarehouseGoodCategory','category_id','category_id');
    }
}
