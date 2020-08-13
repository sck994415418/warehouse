<?php

namespace app\admin\controller;

use app\admin\model\SckWarehouseGoodLog;
use \think\Db;
use \think\Cookie;
use app\admin\controller\Permissions;
use think\Session;
use app\admin\model\Admin;

class Main extends Permissions
{
    public function index()
    {
        //tplay版本号
        $info['tplay'] = TPLAY_VERSION;
        //tp版本号
        $info['tp'] = THINK_VERSION;
        //php版本
        $info['php'] = PHP_VERSION;
        //操作系统
        $info['win'] = PHP_OS;
        //最大上传限制
        $info['upload_size'] = ini_get('upload_max_filesize');
        //脚本执行时间限制
        $info['execution_time'] = ini_get('max_execution_time') . 'S';
        //环境
        $sapi = php_sapi_name();
        if ($sapi = 'apache2handler') {
            $info['environment'] = 'apache';
        } elseif ($sapi = 'cgi-fcgi') {
            $info['environment'] = 'cgi';
        } else {
            $info['environment'] = 'cli';
        }
        //剩余空间大小
        //$info['disk'] = round(disk_free_space("/")/1024/1024,1).'M';
        $this->assign('info', $info);


        /**
         *网站信息
         */
        $web['user_num'] = Db::name('admin')->count();
        $ip_ban = Db::name('webconfig')->value('black_ip');
        $web['ip_ban'] = empty($ip_ban) ? 0 : count(explode(',', $ip_ban));

        $web['file_num'] = Db::name('attachment')->count();
        $web['status_file'] = Db::name('attachment')->where('status', 0)->count();
        $web['ref_file'] = Db::name('attachment')->where('status', -1)->count();
        $web['message_num'] = Db::name('messages')->count();
        $web['look_message'] = Db::name('messages')->where('is_look', 0)->count();


        //登陆次数和下载次数
        $today = date('Y-m-d');

        //取当前时间的前十四天
        $date = [];
        $date_string = '';
        for ($i = 9; $i > 0; $i--) {
            $date[] = date("Y-m-d", strtotime("-{$i} day"));
            $date_string .= date("Y-m-d", strtotime("-{$i} day")) . ',';
        }
        $date[] = $today;
        $date_string .= $today;
        $web['date_string'] = $date_string;

        $login_sum = '';
        foreach ($date as $k => $val) {
            $min_time = strtotime($val);
            $max_time = $min_time + 60 * 60 * 24;
            $where['create_time'] = [['>=', $min_time], ['<=', $max_time]];
            $login_sum .= Db::name('admin_log')->where(['admin_menu_id' => 50])->where($where)->count() . ',';
        }
        $web['login_sum'] = $login_sum;

        $this->assign('web', $web);

        //库存不足
        $good_warn = db('sck_warehouse_good')->where(['good_delete' => 0])->field('create_time,good_warn_day,good_id,good_number,good_warn')->select();
        if (!empty($good_warn)) {
            $warn_arr = [];
            $warn_day_arr = [];
            foreach ($good_warn as $k => $v) {
                if ($good_warn[$k]['good_number'] <= 0 || $good_warn[$k]['good_number'] <= $good_warn[$k]['good_warn']) {
                    $warn_arr[] = $good_warn[$k]['good_id'];
                }
                if ($good_warn[$k]['good_warn_day'] > 0) {
                    $start_time = strtotime(date('Y-m-d H:i:s', time() - 3600 * 24 * $good_warn[$k]['good_warn_day']));
                    $end_time = strtotime(date('Y-m-d H:i:s', time()));
                    if ($start_time >= strtotime($good_warn[$k]['create_time']) and $good_warn[$k]['good_number'] > 0) {
                        if ($start_time > $good_warn[$k]['create_time']) {
                            $have = db('sck_warehouse_good_log')
                                ->where(['good_id' => $good_warn[$k]['good_id'], 'good_status' => 2])
                                ->whereTime('create_time', 'between', [$start_time, $end_time])
                                ->find();
                            if (empty($have)) {
                                $warn_day_arr[] = $good_warn[$k]['good_id'];
                            }
                        }
                    }
                }
            }
        } else {
            $warn_arr = [];
            $warn_day_arr = [];
        }
        $good_warn_count = count($warn_arr);
        $good_warn_day_count = count($warn_day_arr);
        $this->assign('good_warn_count', $good_warn_count);
        $this->assign('good_warn_day_count', $good_warn_day_count);
        $this->assign('good_warn', implode(',', $warn_arr));
        $this->assign('good_warn_day', implode(',', $warn_day_arr));
        //客户数
        $client_count = db('sck_client')->count();
        $this->assign('client_count', $client_count);
        $logModel = new SckWarehouseGoodLog();
//php获取今日开始时间戳和结束时间戳
        $today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $day_money = $logModel
            ->where(['admin_id' => Session::get('admin'), 'good_status' => 2, 'create_time' => [['>=', $today_start], ['<=', $today_end]]])
            ->sum('good_total');
        //php获取本月起始时间戳和结束时间戳
        $thismonth_start = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $thismonth_end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        $year_money = $logModel
            ->where(['admin_id' => Session::get('admin'), 'good_status' => 2, 'create_time' => [['>=', $thismonth_start], ['<=', $thismonth_end]]])
            ->sum('good_total');
        $this->assign('day_money', $day_money);
        $this->assign('year_money', $year_money);

        //本月初 月末
        $year_start = strtotime(date('Y-m-01', strtotime(date('Y-m-d'))));
        $year_end = strtotime(date('Y-m-t', strtotime(date('Y-m-d'))));
        //上月初 月末
        $last_month = strtotime(date('Y-m-01', strtotime('-1 month')));
        $month_end = strtotime(date('Y-m-t', strtotime('-1 month')));
        //获取本季度
        $season = ceil(date('n') / 3);
        $quarter_start = strtotime(date('Y-m-01', mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date('Y'))));
        $quarter_end = strtotime(date('Y-m-t', mktime(0, 0, 0, $season * 3, 1, date('Y'))));
        //获取上季度
        $preceding_quarter = strtotime(date('Y-m-01', mktime(0, 0, 0, ($season - 2) * 3 + 1, 1, date('Y'))));
        $preceding_quarter_end = strtotime(date('Y-m-t', mktime(0, 0, 0, ($season - 1) * 3, 1, date('Y'))));
        $data = [];
        $month = [];
        $quarter = [];
        $preceding = [];
        $adminid = (new Admin())
            ->where(['admin_status' => 1,])
            ->field('id,nickname')
            ->select();

        foreach ($adminid as $key => $val) {
            //上月业绩
            $data[$key]['id'] = $val['id'];
            $data[$key]['nickname'] = $val['nickname'];
            $data[$key]['good_total'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2])->sum('good_total');
            $data[$key]['lowest_price'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $last_month], ['<=', $month_end]],'is_return'=>0])->sum('lowest_price');
            //查询本月业绩
            $month[$key]['id'] = $val['id'];
            $month[$key]['nickname'] = $val['nickname'];
            $month[$key]['good_total'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $year_start], ['<=', $year_end]],'is_return'=>0])->sum('good_total');
            $month[$key]['lowest_price'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $year_start], ['<=', $year_end]],'is_return'=>0])->sum('lowest_price');
            //查询本季度
            $quarter[$key]['id'] = $val['id'];
            $quarter[$key]['nickname'] = $val['nickname'];
            $quarter[$key]['good_total'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $quarter_start], ['<=', $quarter_end]],'is_return'=>0])->sum('good_total');
            $quarter[$key]['lowest_price'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $quarter_start], ['<=', $quarter_end]],'is_return'=>0])->sum('lowest_price');
            //查询上季度
            $preceding[$key]['id'] = $val['id'];
            $preceding[$key]['nickname'] = $val['nickname'];
            $preceding[$key]['good_total'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $preceding_quarter], ['<=', $preceding_quarter_end]]])->sum('good_total');
            $preceding[$key]['lowest_price'] = $logModel->where(['admin_id' => $val['id'], 'good_status' => 2, 'create_time' => [['>=', $preceding_quarter], ['<=', $preceding_quarter_end]],'is_return'=>0])->sum('lowest_price');
        }
        $a = $this->arraySort($data, 'good_total', SORT_DESC);
        $arr2[0] = array_column($a, 'nickname');
        $arr2[1] = array_column($a, 'good_total');
        $arr2[2] = array_column($a, 'lowest_price');
        $this->assign('data',$arr2);

        $b = $this->arraySort($month, 'good_total', SORT_DESC);

        $this->assign('b',$b);
        $arr1[0] = implode(',',array_column($b, 'nickname'));
        $arr1[1] = implode(',',array_column($b, 'good_total'));
        $arr1[2] = implode(',',array_column($b, 'lowest_price'));
        $this->assign('month',$arr1);

        $c = $this->arraySort($quarter, 'good_total', SORT_DESC);
        $this->assign("c",$c);
        $arr3[0] = array_column($c, 'nickname');
        $arr3[1] = array_column($c, 'good_total');
        $arr3[2] = array_column($c, 'lowest_price');
        $this->assign('quarter',$arr3);

        $d = $this->arraySort($preceding, 'good_total', SORT_DESC);
        $arr4[0] = array_column($d, 'nickname');
        $arr4[1] = array_column($d, 'good_total');
        $arr4[2] = array_column($d, 'lowest_price');
        $this->assign('quarter',$arr4);
        foreach($c as $k=>$v){
            foreach($d as $key=>$val){
                if($v['id'] == $val['id']){
                    if($v['good_total'] == 0){
                        $v['good_total'] = 1;
                    }
                    if($val['good_total'] == 0){
                        $val['good_total'] = 1;
                    }
                    $c[$k]['increase'] =round(($v['good_total']-$val['good_total'])/$val['good_total']*100,2).'%'; ;
                }
            }
        }
        $this->assign('c',$c);
        return $this->fetch();
    }

    /**
     * 二维数组根据某个字段排序
     * @param array $array 要排序的数组
     * @param string $keys 要排序的键字段
     * @param string $sort 排序类型  SORT_ASC     SORT_DESC
     * @return array 排序后的数组
     */
    public function arraySort($array, $keys, $sort = SORT_DESC)
    {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }
}
