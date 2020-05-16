<?php
namespace app\admin\model;

use \think\Model;
class SckClient extends Model
{
    public function address()
    {
        return $this->belongsTo('Address','client_position_id','id2|id3|id4|id5');
    }
}
