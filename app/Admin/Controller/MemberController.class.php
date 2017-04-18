<?php

// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rainfer.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rainfer <81818832@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AuthController;
use Org\Util\Stringnew;

class MemberController extends AuthController {
    /*
     * 用户管理
     */

    public function member_list() {
        $key = I('key');
        if ($key) {
            $map['member_list_name|member_list_tel'] = array('like', "%" . $key . "%");
        }
        //查询：时间格式过滤
        $sldate = I('reservation', ''); //获取格式 2015-11-12 - 2015-11-18
        $arr = explode(" - ", $sldate); //转换成数组

        if (count($arr) == 2) {
            $arrdateone = strtotime($arr[0]);
            $arrdatetwo = strtotime($arr[1] . ' 23:59:59');
            $map['member_list_addtime'] = array(array('egt', $arrdateone), array('elt', $arrdatetwo), 'AND');
        }
        //dump($map);
        /*
         * 分页操作
         */
        $count = M('member_list')->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $member_list = D('Member_list')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order('member_list_addtime desc')->relation(true)->select();
        $this->assign('member_list', $member_list);
        $this->assign("sldate", $sldate);
        $this->assign('page', $show);
        $this->assign('val', $key);
        $this->display();
    }

    /*
     * 添加用户界面
     */

    public function member_add() {

        $this->display();
    }
    
    // 客户详情页
    public function member_show() {
        // 基本信息
        $member_list_edit = M('member_list')->where(array('member_list_id' => I('member_list_id')))->find();
        $this->assign('member_list_edit', $member_list_edit);
        // 相关产品
        $products = M('member_product')->where(array('member_id' => I('member_list_id')))->select();
        $this->assign("products", $products);
        
        $this->display();
    }

    /*
     * 添加用户操作
     */

    public function member_runadd() {
        if (!IS_AJAX) {
            $this->error('提交方式不正确', U('member_list'), 0);
        } else {
            $member_list_salt = Stringnew::randString(10);
            $sl_data = array(
                'member_list_name' => I('member_list_name'),
                'member_list_sex' => I('member_list_sex'),
                'member_list_tel' => I('member_list_tel'),
                'member_list_addtime' => time(),
                'member_list_remark' => I('member_list_remark'),
                'member_list_birth' => strtotime(I('member_list_birth'))
            );
            $rst = M('member_list')->add($sl_data);
            if ($rst !== false) {
                $this->success('客户添加成功', U('member_list'), 1);
            } else {
                $this->error('客户添加失败', U('member_list'), 0);
            }
        }
    }

    /*
     * 修改用户信息界面
     */

    public function member_edit() {

        $member_list_edit = M('member_list')->where(array('member_list_id' => I('member_list_id')))->find();
        $this->assign('member_list_edit', $member_list_edit);

        $this->display();
    }

    /*
     * 修改用户操作
     */

    public function member_runedit() {
        if (!IS_AJAX) {
            $this->error('提交方式不正确', U('member_list'), 0);
        } else {

            $sl_data['member_list_id'] = I('member_list_id');

            $sl_data['member_list_name'] = I('member_list_name');
            $sl_data['member_list_sex'] = I('member_list_sex');
            $sl_data['member_list_tel'] = I('member_list_tel');
            $sl_data['member_list_remark'] = I('member_list_remark');
            $sl_data['member_list_birth'] = strtotime(I('member_list_birth'));
            M('member_list')->save($sl_data);
            $this->success('会员修改成功', U('member_list'), 1);
        }
    }


    // 导出客户
    public function member_export() {
        // 筛选客户
        $map['member_list_addtime'] = array("egt", strtotime(I("start_time") . " 00:00:00"));
        $map['member_list_addtime'] = array("elt", strtotime(I("end_time") . " 23:59:59"));
        $members = M("member_list")->select();

        import("Org.Util.PHPExcel");
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new \PHPExcel();
        import("Org.Util.PHPExcel.Reader.Excel5");
        /* 设置excel的属性 */
        $objPHPExcel->getProperties()->setCreator("MetLife")//创建人
                ->setKeywords("excel")//关键字
                ->setCategory("result file"); //种类
        //第一行数据
        $objPHPExcel->setActiveSheetIndex(0);
        $active = $objPHPExcel->getActiveSheet();
        $titles = array("客户姓名", "客户性别", "客户联系方式", "添加时间", "备注");
        foreach ($titles as $i => $name) {
            $ck = num2alpha($i++) . '1';
            $active->setCellValue($ck, $name);
        }

        $fields = array("member_list_name", "member_list_sex", "member_list_tel", "member_list_remark");
        //填充数据
        foreach ($members as $k => $v) {
            $k = $k + 1;
            $num = $k + 1; //数据从第二行开始录入
            $objPHPExcel->setActiveSheetIndex(0);
            foreach ($fields as $i => $name) {
                $ck = num2alpha($i++) . $num;
                if ($name == "member_list_sex") {
                    if ($v[$name] == 1) {
                        $active->setCellValue($ck, "先生");
                    } elseif ($v[$name] == 2) {
                        $active->setCellValue($ck, "女士");
                    } else {
                        $active->setCellValue($ck, "保密");
                    }
                } else {
                    $active->setCellValue($ck, $v[$name]);
                }
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle("客户表");
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . "客户表" . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    // 导入客户页面
    public function member_import() {
        $this->display();
    }

    // 导入客户操作
    public function member_runimport() {
        import("Org.Util.PHPExcel");
        $PHPExcel = new \PHPExcel();
        import("Org.Util.PHPExcel.Reader.Excel5");

        if (!empty($_FILES ['file_stu'] ['name'])) {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file_stu'] ['name']);
            $file_type = $file_types [count($file_types) - 1];
            /* 判别是不是.xls文件，判别是不是excel文件 */
            if (strtolower($file_type) != "xls") {
                $this->error('不是Excel文件，重新上传', U('excel_import'), 0);
            }
            /* 设置上传路径 */
            $savePath = './public/excel/';
            /* 以时间来命名上传的文件 */
            $str = time('Ymdhis');
            $file_name = $str . "." . $file_type;

            if (!copy($tmp_file, $savePath . $file_name)) {
                $this->error('上传失败', U('excel_import'), 0);
            }

            $res = $this->read($savePath . $file_name);
            // 开启事务
            M('member_list')->startTrans();
            foreach ($res as $k => $v) {
                if ($k != 1) {
                    // 导入数据检验
                    if (!$v[0]) {
                        $flag = false;
                        $this->error("第" . $k . "行客户姓名不能为空", U('member_import'));
                    } elseif (!preg_match('/[1-3]+/', $v[1])) {
                        $flag = false;
                        $this->error("第" . $k . "行客户性别格式错误", U('member_import'));
                    }elseif (!preg_match('/^1[34578]\d{9}$/', $v[1])) {
                        $flag = false;
                        $this->error("第" . $k . "行客户手机号错误", U('member_import'));
                    }
                    if (!$flag) {
                        M("member_list")->rollback();
                        return false;
                    }
                    // 组装数据
                    $data ['member_list_name'] = $v[0];

                    $data ['member_list_sex'] = $v[1];
                    $data ['member_list_tel'] = $v[2];
                    $data ['member_list_addtime'] = time();
                    $data ['member_list_remark'] = $v[3];

                    $result = M('member_list')->add($data);
                    if (!$result) {
                        $this->error('导入数据库失败', U('member_import'), 0);
                    } else {
                        $this->success("导入数据成功", U('member_list'));
                    }
                }
            }
            // 提交事务
            M("member_list")->commit();
            if (!$res) {
                $this->error('数据处理失败', U('excel_import'), 0);
            }
        }
    }

    /*
     * 会员删除
     */

    public function member_del() {
        $p = I('p');
        $rst = M('member_list')->where(array('member_list_id' => I('member_list_id')))->delete();
        if ($rst !== false) {
            $this->success('会员删除成功', U('member_list', array('p' => $p)), 1);
        } else {
            $this->error('会员删除失败', U('member_list', array('p' => $p)), 0);
        }
    }
   
    // 读取excel文件内容
    private function read($filename, $encode = 'utf-8') {
        $objReader = \PHPExcel_IOFactory::createReader(Excel5);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

}
