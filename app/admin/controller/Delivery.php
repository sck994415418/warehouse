<?php

namespace app\admin\controller;

use think\Session;
use app\admin\controller\Exel;
use PHPExcel;
class Delivery extends Permissions
{
//    打印销售单
    public function log_print()
    {
        $id = request()->get();
        $data = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_client sc', 'swgl.client_id = sc.client_id')
//            ->join('address ads','ads.id5 = sc.client_position_id or ads.id4 = sc.client_position_id','left')
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_warehouse_good_log_pay swglp','swglp.log_id = swgl.log_id','left')
            ->field("swgl.*,sc.client_id,sc.client_name,sc.client_company,sc.client_desc,sc.client_phone,sc.client_wechat,sc.client_position_details,swg.good_sku,swg.good_coding,swg.good_position,swglp.pay_price,swglp.pay_total")
            ->select();
        $arr = $data;
        foreach($data as $k=>$v){
            if(isset($arr[$k])){
                $data[$k]['goods'][] =[
                    'good_id'=>$v['good_id'],
                    'good_name'=>$v['good_name'],
                    'good_desc'=>$v['good_desc'],
                    'good_number'=>$v['good_number'],
                    'good_amount'=>$v['good_amount'],
                    'good_price'=>$v['good_price'],
                    'good_total'=>$v['good_total'],
                    'good_sku'=>$v['good_sku'],
                    'good_coding'=>$v['good_coding'],
                    'good_position'=>$v['good_position'],
                ];
                $data[$k]['pay_total'] = empty($v['pay_total'])?$v['good_total']:$v['pay_total'];
                $data[$k]['pay_price'] =empty($v['pay_price'])?0:$v['pay_price'];
                unset($data[$k]['good_id'],$data[$k]['good_name'],$data[$k]['good_desc'],$data[$k]['good_number'],$data[$k]['good_amount'], $data[$k]['good_price'], $data[$k]['good_total']);

                foreach($arr as $key=>$val){
                    unset($arr[$k]);
                    if($v['client_id'] == $val['client_id'] && $v['log_id'] != $val['log_id']){
                        $data[$k]['goods'][] = [
                            'good_id'=>$val['good_id'],
                            'good_name'=>$val['good_name'],
                            'good_desc'=>$val['good_desc'],
                            'good_number'=>$val['good_number'],
                            'good_amount'=>$val['good_amount'],
                            'good_price'=>$val['good_price'],
                            'good_total'=>$val['good_total'],
                            'good_sku'=>$val['good_sku'],
                            'good_coding'=>$val['good_coding'],
                            'good_position'=>$val['good_position'],

                        ];
                        $data[$k]['pay_total'] += empty($val['pay_total'])?$val['good_total']:$val['pay_total'];
                        $data[$k]['pay_price'] += empty($val['pay_price'])?0:$val['pay_price'];
                        unset($arr[$key]);
                        unset($data[$key]);

                    }
                }
                $data[$k]['admin_id'] = db('admin')->where('id',$v['admin_id'])->value('nickname');
                $data[$k]['admin_id_name'] = db('admin')->where('id',Session::get('admin'))->value('nickname');
            }
        }
        if ($data) {
            addlog();
            db("sck_warehouse_good_log")->where(['log_id'=>['in',$id['data']]])->setInc('is_print',1);
            return view("", ['data' => $data]);
        } else {
            $this->error("请联系库管出库后再次操作");
        }
    }
    //商品出库
    public function good_out(){
        if(request()->isPost()){
            $data = request()->post();

            $data = db('sck_warehouse_good_log')
                ->where(['log_id' => ['in', $data['data']]])
                ->update(['is_delivery'=>1]);
            if($data){
                $this->success("出库成功");
            }else{
                $this->error("出库失败");
            }
        }else{
            $this->error("请求错误");
        }

    }
//    导出excel信息
    public function excel(){
        $id = request()->get();

        $arr = db('sck_warehouse_good_log')
            ->alias('swgl')
            ->where(['swgl.log_id' => ['in', $id['data']]])
            ->join('sck_warehouse_good swg','swgl.good_id = swg.good_id')
            ->join('sck_warehouse_good_log_pay swglp','swglp.log_id = swgl.log_id','left')
            ->field("swgl.*,swg.good_sku,swg.good_coding,swglp.pay_price,swglp.pay_total")
            ->select();
        if ($arr) {
            $head = ['序号', '货物或应税劳务、服务名称', '计量单位', '规格型号', '数量', '金额', '税率', '商品税目', '折扣金额', '税额','折扣税额','折扣率','单价','价格方式','税收分类编码版本号','税收分类编码','企业商品编码','使用优惠政策标识','零税率标识','优惠政策说明','中外合作油气田标识'];
            $keys = ['ids', 'good_name','unit','good_sku', 'good_amount', 'good_total', 'tax_rate', 'tax_items', 'discount', 'tax_break','tax_deduction','discount_rate','good_price','way','code_number','classification_code','commodity_code','policy_labels','Zero_rate_mark','explain','identification'];
            foreach($arr as $k=>$v){
                $data[$k]['ids'] = $k+1;
                $data[$k]['good_name'] = $v['good_name'];
                $data[$k]['unit'] = '个';
                $data[$k]['good_sku'] = $v['good_sku'];
                $data[$k]['good_amount'] = number_format($v['good_amount'],6);
                $data[$k]['good_total'] = number_format($v['good_total'],2);
                $data[$k]['tax_rate']= '0.13';
                $data[$k]['tax_items']= "";
                $data[$k]['discount']="";
                $data[$k]['tax_break']=round($v['good_total']/1.13*0.13,2);
                $data[$k]['tax_deduction']="";
                $data[$k]['discount_rate']="";
                $data[$k]['good_price']=number_format($v['good_price'],6);
                $data[$k]['way']=1;
                $data[$k]['code_number']='35.0';
                $data[$k]['classification_code']=0;
                $data[$k]['commodity_code']=0;
                $data[$k]['policy_labels']=0;
                $data[$k]['Zero_rate_mark']=0;
                $data[$k]['explain']=0;
                $data[$k]['identification']=0;
            }
//            dump($data);die;
            addlog();
            (new Exel())->excelExport('清单表',$head,$data);
//            (new Exel())->outdata('清单表',$data,$head,$keys);
        } else {
            $this->error("未知错误,请重新选择");
        }
    }
}