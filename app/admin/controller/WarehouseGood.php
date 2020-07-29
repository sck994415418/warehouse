<?php

namespace app\admin\controller;

use app\admin\model\SckWarehouseGoodLog;
use PHPExcel_Reader_CSV;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
use think\Db;
use app\admin\model\SckWarehouseGood as WarehouseGoodModel;
use think\Exception;
use think\exception\PDOException;
use think\Session;
use think\Validate;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class WarehouseGood extends Permissions
{
    public function index()
    {
        $category = db('sck_warehouse_good_category')
            ->where(['parent_id'=>0])
            ->select();
        $this->assign('category',$category);
        $model = new WarehouseGoodModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['good_name | good_desc'] = ['like', '%' . $post['keywords'] . '%'];
        }
        if (isset($post['good_arr']) and !empty($post['good_arr'])) {
//            $post['good_arr'] = json_decode($post['good_arr'],true);
            $post['good_arr'] = explode(',',$post['good_arr']);
            $where['good_id'] = ['in',$post['good_arr']];
        }
        if (isset($post['time']) and !empty($post['time'])) {
            $start_time = strtotime(substr($post['time'],0,strripos($post['time'],' - ')));
            $end_time = strtotime(substr($post['time'],strripos($post['time'],' - ')+3));
            $where['create_time']=['between',[$start_time,$end_time]];
        }
        if (isset($post['category_id']) and !empty($post['category_id'])) {
            $where['category_id'] =  $post['category_id'];
        }
        if (isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
        }
        if(isset($post['good_coding']) && !empty($post['good_coding'])){
            $where['good_coding'] =  $post['good_coding'];
        }
        $where['good_delete'] = ['neq',1];
        $data = empty($where) ? $model
            ->order('create_time desc')
            ->paginate(20)
            ->each(function ($k,$v){
                if($k['good_warn_day']>0){
                    $start_time = strtotime(date('Y-m-d H:i:s',time()-3600*24*$k['good_warn_day']));
                    $end_time = strtotime(date('Y-m-d H:i:s',time()));
                    if($start_time>strtotime($k['create_time']) and $k['good_number']>0){
                        $have = db('sck_warehouse_good_log')
                            ->where(['good_id'=>$k['good_id'],'good_status'=>2])
                            ->whereTime('create_time','between',[$start_time,$end_time])
                            ->find();
                        if(empty($have)){
                            $k['good_warn_day_warn']=1;
                        }else{
                            $k['good_warn_day_warn']=0;
                        }
                    }else{
                        $k['good_warn_day_warn']=0;
                    }

                }


                $good_log = db('sck_warehouse_good_log')
                    ->where(['good_id'=>$k['good_id'],'good_status'=>1])
                    ->order('create_time','desc')
                    ->limit(2)
                    ->select();
                if(empty($good_log)){
                    $k['good_lowest_price']=0;
                }else{
                    $total_price = array_sum(array_column($good_log,'good_total'));
                    $total_amount = array_sum(array_column($good_log,'good_amount'));
                    $k['good_lowest_price'] = round($total_price/$total_amount,2);
                }
            })
            : $model->where($where)
                ->order('create_time desc')
                ->paginate(20, false, ['query' => $this->request->param()])
                ->each(function ($k,$v){
                    if($k['good_warn_day']>0){
                        $start_time = strtotime(date('Y-m-d H:i:s',time()-3600*24*$k['good_warn_day']));
                        $end_time = strtotime(date('Y-m-d H:i:s',time()));
                        if($start_time>=strtotime($k['create_time']) and $k['good_number']>0){
                            $have = db('sck_warehouse_good_log')
                                ->where(['good_id'=>$k['good_id'],'good_status'=>2])
                                ->whereTime('create_time','between',[$start_time,$end_time])
                                ->find();
                            if(empty($have)){
                                $k['good_warn_day_warn']=1;
                            }else{
                                $k['good_warn_day_warn']=0;
                            }
                        }else{
                            $k['good_warn_day_warn']=0;
                        }
                    }
                    $good_log = db('sck_warehouse_good_log')
                        ->where(['good_id'=>$k['good_id'],'good_status'=>1])
                        ->order('create_time','desc')
                        ->limit(2)
                        ->select();
                    if(empty($good_log)){
                        $k['good_lowest_price']=0;
                    }else{
                        $total_price = array_sum(array_column($good_log,'good_total'));
                        $total_amount = array_sum(array_column($good_log,'good_amount'));
                        $k['good_lowest_price'] = round($total_price/$total_amount,2);
                    }
                });
//        dump($where);die;
        $this->assign('data', $data);
        return $this->fetch();
    }


    public function publish()
    {
        $model = new WarehouseGoodModel();
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $validate = new Validate([
                ['good_name', 'require', '商品名称不能为空'],
                ['good_price', 'require', '商品单价不能为空'],
                ['good_total', 'require', '商品总价不能为空'],
                ['good_amount', 'require', '商品库存不能为空'],
            ]);
            $post['good_number'] = $post['good_amount'];
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $admin_id = Session::get('admin');
            if(!$admin_id){
                $json = ['code'=>0,'msg'=>'页面错误，刷新后重试！','url'=>''];
                return $json;
            }
            $post['admin_id'] = $admin_id;
            $post['create_time'] = time();
            Db::startTrans();
            try {
                $ok = $model->allowField(true)->save($post);
                $good_id = Db::name('sck_warehouse_good')->getLastInsID();
                if($ok){
                    $LogModel = new SckWarehouseGoodLog();
                    $post['good_id'] = $good_id;
                    $insert = $LogModel->allowField(true)->save($post);
                    if($insert){
                        addlog($good_id);
                        $json = ['code'=>1,'msg'=>'添加成功','url'=>'index'];
                    }else{
                        throw new \Exception('添加失败，请重试!');
                    }
                }else{
                    throw new \Exception('添加失败，请重试!');
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $json = ['code'=>0,'msg'=>$e->getMessage()];
            }
            return $json;
        } else {
            $data = db('sck_warehouse_good_category')
                ->where(['parent_id'=>0])
                ->select();
            $project = db('project')->where(['status'=>1])->order('id desc')->select();
            $this->assign('category',$data);
            $supplier = db('sck_supplier')->select();
            $this->assign('supplier',$supplier);
            $this->assign('project',$project);
            return $this->fetch();
        }
    }

    public function edit()
    {
        $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
        $model = new WarehouseGoodModel();
        if ($good_id > 0) {
            if ($this->request->isPost()) {
                $post = $this->request->post();
                $validate = new Validate([
                    ['good_name', 'require', '商品名称不能为空'],
                ]);
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }

                Db::startTrans();
                try {
                    $ok = $model->allowField(true)->save($post, ['good_id' => $good_id]);
                    if($ok){
                        $LogModel = new SckWarehouseGoodLog();
                        $insert = $LogModel->allowField(true)->save($post, ['good_id' => $good_id]);
                        if($insert){
                            addlog($good_id);
                            $json = ['code'=>1,'msg'=>'修改成功'];
                        }else{
                            throw new \Exception('修改失败，请重试!');
                        }
                    }else{
                        throw new \Exception('修改失败，请重试!');
                    }
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $json = ['code'=>0,'msg'=>$e->getMessage()];
                }
                return $json;

            } else {
                $data = $model->where('good_id', $good_id)->find();
                $this->assign('data', $data);
                return $this->fetch();
            }
        } else {
            return $this->error('页面错误，请重试！');
        }
    }

    /**
     * 供应商删除
     * @throws Exception
     * @throws PDOException
     */
    public function delete()
    {
        if ($this->request->isAjax()) {
            $good_id = $this->request->has('good_id') ? $this->request->param('good_id', 0, 'intval') : 0;
            $model = new WarehouseGoodModel();
            if (false == db('sck_warehouse_good')->where('good_id', $good_id)->update(['good_delete'=>1])) {
                return $this->error('删除失败');
            } else {
                addlog($good_id);//写入日志
                return $this->success('删除成功', 'admin/WarehouseGood/index');
            }
        }
    }

    public function import(){
        if(request()->isGet()){
////            Db::startTrans();
//            $log_id = Db('sck_warehouse_good_log')
//                ->where(['admin_id'=>30])
//                ->column('log_id');
//            $good_id = Db('sck_warehouse_good_log')
//                ->where(['admin_id'=>30])
//                ->column('good_id');
//            Db('sck_warehouse_good_log')->delete($log_id);
//            Db('sck_warehouse_good')->delete($good_id);
////            Db::commit();
////            Db::rollback();

            return view();
        }elseif(request()->isPost()){
            Db::startTrans();
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' .DS.'uploads'. DS . 'excel');
            if($info){
                //获取文件所在目录名
                $path=ROOT_PATH . 'public' . DS.'uploads'.DS .'excel'.DS.$info->getSaveName();
                $extension = strtolower( pathinfo($path, PATHINFO_EXTENSION) );
                if ($extension =='xlsx') {
                    $objReader = new PHPExcel_Reader_Excel2007();
                    $objPHPExcel = $objReader ->load($path);
                } else if ($extension =='xls') {
                    $objReader = new PHPExcel_Reader_Excel5();
                    $objPHPExcel = $objReader ->load($path);
                } else if ($extension=='csv') {
                    $PHPReader = new PHPExcel_Reader_CSV();
                    //默认输入字符集
                    $PHPReader->setInputEncoding('GBK');
                    //默认的分隔符
                    $PHPReader->setDelimiter(',');
                    //载入文件
                    $objPHPExcel = $PHPReader->load($path);
                }
//                $objPHPExcel = $objReader->load($path,$encode='utf-8');//获取excel文件
                $sheet = $objPHPExcel->getSheet(0); //激活当前的表
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                $a=0;
                //将表格里面的数据循环到数组中
                for($i=2;$i<=$highestRow-1;$i++)
                {
                    //*为什么$i=2? (从第二行开始，才是我们要的数据。)
                    $data[$a]['good_name'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();//商品名称
                    $data[$a]['good_desc'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();//商品简介/备注
                    $data[$a]['good_sku'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();//商品规格型号
                    $data[$a]['good_coding'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();//商品编码
                    $data[$a]['good_warn_day'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//积压提醒
                    $data[$a]['good_warn'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();//库存预警值
                    $data[$a]['category_id'] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();//商品分类id
                    $data[$a]['good_amount'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();//商品库存
                    $data[$a]['good_price'] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();//商品单价
                    $data[$a]['good_total'] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();//商品总价
                    $data[$a]['good_position'] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();//仓库位置
                    $data[$a]['good_number'] = $data[$a]['good_amount'];//商品剩余库存
                    $data[$a]['good_status'] = 1;
                    $data[$a]['create_time'] = time();
                    $data[$a]['update_time'] = time();
                    // 这里的数据根据自己表格里面有多少个字段自行决定
                    $a++;
                }
                //往数据库添加数据
                $model = new WarehouseGoodModel();
                $res = $model->insertAll($data);
                $testres = $model->getLastInsID();
                for ($i=0; $i<$res; $i++) {
                    $data[$i]['good_id'] = (int)$testres++;
                    $data[$i]['admin_id'] = Session::get('admin');
                    unset($data[$i]['category_id'],$data[$i]['good_warn'],$data[$i]['good_warn_day'],$data[$i]['good_sku'],$data[$i]['good_coding'],$data[$i]['good_position']);
                }
                $LogModel = new SckWarehouseGoodLog();
                $insert = $LogModel->insertAll($data);
                if($res && $insert){
                    Db::commit();
                    $this->success('操作成功！');
                }else{
                    Db::rollback();
                    $this->error('操作失败！');
                }
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
    }
     public function barcode(){
         $barcode = new BarcodeGenerator();
         $barcode->setText("0123456789");
         $barcode->setType(BarcodeGenerator::Code128);
         $barcode->setScale(2);
         $barcode->setThickness(25);
         $barcode->setFontSize(10);
         $code = $barcode->generate();
         echo '<img src="data:image/png;base64,' . $code . '" />';
     }
}
