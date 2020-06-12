<?php
/**
 * Created by PhpStorm.
 * User: 37445
 * Date: 2019/9/20
 * Time: 16:10
 */

namespace app\admin\controller;


use PHPExcel_IOFactory;
use think\Controller;

use think\Loader;

//使用Spreadsheet类
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//xlsx格式类
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//可以生成多种格式类
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPExcel;
class Exel extends Controller
{
    public function out()
    {
        //设置表头：
        $head = ['订单编号', '收获地址', '商品', '购买数量', '支付金额','支付邮费'];
        //数据中对应的字段，用于读取相应数据：
        $keys = ['order_id', 'user_address', 'cart_id', 'total_num','total_price','pay_postage'];
        $orders = db("store_order")->field($keys)->select();
        $this->outdata('订单表', $orders, $head, $keys);

    }

    /**
     * 导出excel表
     * $data：要导出excel表的数据，接受一个二维数组
     * $name：excel表的表名
     * $head：excel表的表头，接受一个一维数组
     * $key：$data中对应表头的键的数组，接受一个一维数组
     * 备注：此函数缺点是，表头（对应列数）不能超过26；
     *循环不够灵活，一个单元格中不方便存放两个数据库字段的值
     */
    public function outdata($name = '测试表', $data = [], $head = [], $keys = [])
    {
        $count = count($head);  //计算表头数量

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 65; $i < $count + 65; $i++) {     //数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $head[$i - 65]);
        }

        /*--------------开始从数据库提取信息插入Excel表中------------------*/


        foreach ($data as $key => $item) {             //循环设置单元格：
            //$key+2,因为第一行是表头，所以写到表格时   从第二行开始写

            for ($i = 65; $i < $count + 65; $i++) {     //数字转字母从65开始：
                $sheet->setCellValue(strtoupper(chr($i)) . ($key + 2), $item[$keys[$i - 65]]);
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setAutoSize(true); //固定列宽
                $spreadsheet->getActiveSheet()->getStyle ('E')->getNumberFormat()->setFormatCode ("0.000000");
                $spreadsheet->getActiveSheet()->getStyle ('F')->getNumberFormat()->setFormatCode ("0.00");
                $spreadsheet->getActiveSheet()->getStyle ('M')->getNumberFormat()->setFormatCode ("0.000000");
                $spreadsheet->getActiveSheet()->getStyle ('O')->getNumberFormat()->setFormatCode ("0.0");
            }
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
    /**
     *
     * execl数据导出
     * 应用场景：订单导出
     * @param string $title 模型名（如Member），用于导出生成文件名的前缀
     * @param array $cellName 表头及字段名
     * @param array $data 导出的表数据
     *
     * 特殊处理：合并单元格需要先对数据进行处理
     */
    function excelExport($fileName = '', $headArr = [], $data = []) {

        $fileName .= "-" . date("YmdHi",time()) . ".xls";

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties();

        $key = ord("A"); // 设置表头

        foreach ($headArr as $v) {

            $colum = chr($key);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $key += 1;

        }

        $column = 2;

        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach ($data as $key => $rows) { // 行写入

            $span = ord("A");

            foreach ($rows as $keyName => $value) { // 列写入
                $objActSheet->setCellValue(chr($span) . $column, $value);
                $objActSheet->getStyle ('E')->getNumberFormat()->setFormatCode ("0.000000");
                $objActSheet->getStyle ('F')->getNumberFormat()->setFormatCode ("0.00");
                $objActSheet->getStyle ('M')->getNumberFormat()->setFormatCode ("0.000000");
                $objActSheet->getStyle ('O')->getNumberFormat()->setFormatCode ("0.0");
//                $keys = $key+2;
//                $objActSheet->setCellValue('P'.$keys, '=SUBSTITUTE(w'.$keys.',".","")&REPT(0,19-LEN(SUBSTITUTE(w'.$keys.',".","")))');;
//                $objActSheet->setCellValueExplicit ('p'.$key,"0000000000000000000",\PHPExcel_Cell_DataType::TYPE_STRING);
                $span++;

            }

            $column++;

        }

        $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表

        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表

        header('Content-Type: application/vnd.ms-excel');

        header("Content-Disposition: attachment;filename=$fileName");

        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); // 文件通过浏览器下载

        exit();

    }

}