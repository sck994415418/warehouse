<?php

namespace app\admin\model;

use \think\Model;
class SckSupplier extends Model
{
    public function address()
    {
        return $this->belongsTo('Address','supplier_position_id','id2|id3|id4|id5');
    }
}
