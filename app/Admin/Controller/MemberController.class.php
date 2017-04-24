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
        // 判断登录身份
        $admin_id = session("aid");
        $group_id = M("auth_group_access")->where(array("uid" => $admin_id))->getField("group_id");
        if ($group_id == 3) {   // 普通管理员
            $auth = "common";
            $map["admin_id"] = $admin_id;
        }else{
            if(I('admin_id')){
                $map['admin_id'] = I("admin_id");
                $this->admin_id = I("admin_id");
            }
        }
        $this->assign("auth", $auth);
        $key = I('key');
        if ($key) {
            $map['member_list_name|member_list_tel'] = array('like', "%" . $key . "%");
        }
        $status = I("status", 0, "intval");
        if ($status == 1) {
            $map["admin_id"] = array("neq", '');
        } elseif ($status == 2) {
            $map["admin_id"] = array("eq", 0);
        }
        $this->assign("status", $status);
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
        // 获取产品信息
        $products = M("product_list")->select();
        $this->assign("products", $products);
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
            // 保存客户基本信息
            $sl_data = array(
                'member_list_name' => I('member_list_name'),
                'member_list_sex' => I('member_list_sex'),
                'member_list_tel' => I('member_list_tel'),
                'member_list_addtime' => time(),
                'member_list_remark' => I('member_list_remark'),
                'member_list_birth' => strtotime(I('member_list_birth'))
            );
            $res = M('member_list')->add($sl_data);
            if ($res !== false) {
                // 保存客户订单信息
                $products = I("product");
                if (count($products['product_id']) > 0) {
                    $product_data['member_id'] = $res;
                    $product_data['admin_id'] = session("aid");
                    $product_data['addtime'] = time();
                    foreach ($products['product_id'] as $key => $product_id) {
                        $product_data["product_id"] = $product_id;
                        $product_data["total_price"] = $products["total_price"][$key];
                        $product_data["start_time"] = strtotime($products["start_time"][$key]);
                        $product_data["end_time"] = strtotime($products["end_time"][$key]);
                        M("member_product")->add($product_data);
                    }
                }
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
        // 获取产品信息
        $products = M("product_list")->select();
        $this->assign("products", $products);

        $html = "<div class='col-sm-10 productDiv' style='padding-top: 3px;border: 1px gray dashed;margin-bottom: 15px;'><div style='float: right;top:15px'><a class='btn btn-danger delBtn' style='height: 30px;line-height: 10px;'>删除</a></div><div style='margin-bottom: 10px;'><label class='col-sm-2' style='margin-bottom: 0;'>产品名称:</label><select name='product[product_id][]'>";
        foreach ($products as $product) {
            $html .= "<option value='" . $product['product_list_id'] . "'>" . $product['product_list_name'] . "</option>";
        }
        $html .= "</select></div><div style='margin-bottom: 10px;'><label class='col-sm-2' style='margin-bottom: 0'>总金额:</label><input name='product[total_price][]'></div><div style='margin-bottom: 10px;'><label class='col-sm-2' style='margin-bottom: 0'>开始时间:</label><input name='product[start_time][]'><span class='help-inline'>&nbsp;&nbsp;格式:2017-01-01</span></div><div style='margin-bottom: 10px;'><label class='col-sm-2' style='margin-bottom: 0'>结束时间:</label><input name='product[end_time][]'><span class='help-inline'>&nbsp;&nbsp;格式:2017-01-01</span></div></div>";

        $this->assign("html", $html);

        // 客户基本信息
        $member_list_edit = M('member_list')->where(array('member_list_id' => I('member_list_id')))->find();
        $this->assign('member_list_edit', $member_list_edit);
        // 客户购买产品信息
        $member_products = M('member_product')->where(array("member_id"=> I('member_list_id')))->select();
        $this->assign("member_products", $member_products);

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

            // 修改购买产品信息
            // 先删除数据库中这个客户的所有购买产品
            M("member_product")->where(array("member_id"=> I('member_list_id')))->delete();
            // 重新插入
            $products = I("product");
            if (count($products['product_id']) > 0) {
                $product_data['member_id'] = I('member_list_id');
                $product_data['admin_id'] = session("aid");
                foreach ($products['product_id'] as $key => $product_id) {
                    $product_data["product_id"] = $product_id;
                    $product_data["total_price"] = $products["total_price"][$key];
                    $product_data["start_time"] = strtotime($products["start_time"][$key]);
                    $product_data["end_time"] = strtotime($products["end_time"][$key]);
                    if ($products["addtime"][$key]) {
                        $product_data['addtime'] = strtotime($products["addtime"][$key]);
                    } else {
                        $product_data['addtime'] = time();
                    }

                    M("member_product")->add($product_data);
                }
            }

            $this->success('客户修改成功', U('member_list'), 1);
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
                if ($k > 1) {
                    // 导入数据检验
                    $flag = true;
                    if (!$v[0]) {
                        $flag = false;
                        $this->error("第" . $k . "行客户姓名不能为空", U('member_import'));
                    } elseif (!$v[1]) {
                        $flag = false;
                        $this->error("第" . $k . "行客户性别格式错误", U('member_import'));
                    } elseif (!$v[2]) {
                        $flag = false;
                        $this->error("第" . $k . "行客户手机号错误", U('member_import'));
                    }
                    if (!$flag) {
                        M("member_list")->rollback();
                        return false;
                    }

                    $map['member_list_name'] = $v[0];
                    $map['member_list_tel'] = $v[2];
                    $member = M("member_list")->where($map)->find();
                    if ($member) {        // 用户已存在
                        $result = $member['member_list_id'];
                    } else {
                        // 组装数据
                        $data ['member_list_name'] = $v[0];
                        $data ['member_list_sex'] = $v[1];
                        $data ['member_list_tel'] = $v[2];
                        $data['member_list_birth'] = \PHPExcel_Shared_Date::ExcelToPHP($v[3]);
                        $data ['member_list_addtime'] = time();
                        $data ['member_list_remark'] = $v[4];
                        $result = M('member_list')->add($data);
                    }

                    if (!$result) {
                        $this->error('导入数据库失败', U('member_import'), 0);
                    } else {
                        if ($v[4]) {      // 购买了产品
                            $product_data['product_id'] = getField($v[5], "product_list", 'product_list_id', 'product_list_name');
                            $product_data['member_id'] = $result;
                            $product_data['admin_id'] = getField($v[9], "admin", 'admin_id', 'admin_realname');
                            $product_data['total_price'] = $v[6];
                            $product_data['start_time'] = \PHPExcel_Shared_Date::ExcelToPHP($v[7]);
                            $product_data['end_time'] = \PHPExcel_Shared_Date::ExcelToPHP($v[8]);
                            $product_data['addtime'] = $v[10] ? PHPExcel_Shared_Date::ExcelToPHP($v[10]) : time();
                            // 验证数据合法性
                            if (!$product_data['product_id']) {
                                $this->error("第" . $k . "行产品不存在", U('member_import'), 0);
                                M("member_list")->rollback();
                            } elseif (!$product_data['admin_id']) {
                                $this->error("第" . $k . "行负责人不存在", U('member_import'), 0);
                                M("member_list")->rollback();
                            } elseif (!$product_data['total_price']) {
                                $this->error("第" . $k . "行价格不能为空", U('member_import'), 0);
                                M("member_list")->rollback();
                            } elseif (!$product_data['start_time']) {
                                $this->error("第" . $k . "行产品开始时间不能为空", U('member_import'), 0);
                                M("member_list")->rollback();
                            } elseif (!$product_data['end_time']) {
                                $this->error("第" . $k . "行产品结束时间不能为空", U('member_import'), 0);
                                M("member_list")->rollback();
                            }
                            M("member_product")->add($product_data);
                        }
                    }
                }
            }
            // 提交事务
            M("member_list")->commit();
            $this->success("导入数据成功", U('member_list'));
            if (!$res) {
                $this->error('数据处理失败', U('excel_import'), 0);
            }
        }
    }

    /*
     * 客户删除
     */

    public function member_del() {
        $p = I('p');
        $rst = M('member_list')->where(array('member_list_id' => I('member_list_id')))->delete();
        if ($rst !== false) {
            $this->success('客户删除成功', U('member_list', array('p' => $p)), 1);
        } else {
            $this->error('客户删除失败', U('member_list', array('p' => $p)), 0);
        }
    }

    /*     * ****************************************客户产品***************************************************** */

    public function member_product_list() {
        // 判断登录身份
        $admin_id = session("aid");
        $group_id = M("auth_group_access")->where(array("uid" => $admin_id))->getField("group_id");
        if ($group_id == 3) {   // 普通管理员
            $auth = "common";
            $map["admin_id"] = $admin_id;
        }
        $this->assign("auth", $auth);
        $key = I('key');
        $search_map['member_list_name'] = array('like', "%" . $key . "%");
        $search = M('member_list')->where($search_map)->getField('member_list_id', true);
        if ($search) {
            $map['member_id'] = array('in', $search);
        }
        //查询：时间格式过滤
        $sldate = I('reservation', ''); //获取格式 2015-11-12 - 2015-11-18
        $arr = explode(" - ", $sldate); //转换成数组

        if (count($arr) == 2) {
            $arrdateone = strtotime($arr[0]);
            $arrdatetwo = strtotime($arr[1] . ' 23:59:59');
            $map['addtime'] = array(array('egt', $arrdateone), array('elt', $arrdatetwo), 'AND');
        }
        //dump($map);
        /*
         * 分页操作
         */
        $count = M('member_product')->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $member_product_list = M('member_product')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order('addtime desc')->select();
        $this->assign('member_product_list', $member_product_list);
        $this->assign("sldate", $sldate);
        $this->assign('page', $show);
        $this->assign('val', $key);
        $this->display();
    }

    public function member_product_add() {
        $products = M("product_list")->select();
        $this->assign("products", $products);
        $this->display();
    }

    public function member_product_runadd() {
        $data = I("post.");
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $data['addtime'] = time();
        $data['admin_id'] = session('aid');

        if ($data) {
            $res = M('member_product')->add($data);
            if ($res) {
                $this->success('数据添加成功', U('member_product_list'), 1);
            } else {
                $this->error('客户添加成功', U('member_product_list'), 0);
            }
        }
    }

    public function member_product_edit() {
        $member_product_edit = M('member_product')->where(array('id' => I('id')))->find();
        $this->assign("member_product_edit", $member_product_edit);
        $products = M("product_list")->select();
        $this->assign("products", $products);
        $this->display();
    }

    public function member_product_runedit() {
        if (!IS_AJAX) {
            $this->error('提交方式不正确', U('member_list'), 0);
        } else {
            $sl_data['id'] = I('id');
            $sl_data['member_id'] = I('member_id');
            $sl_data['product_id'] = I('product_id');
            $sl_data['start_time'] = strtotime(I('start_time'));
            $sl_data['end_time'] = strtotime(I('end_time'));
            $sl_data['addtime'] = strtotime(I('addtime'));
            M('member_product')->save($sl_data);

            $this->success('数据修改成功', U('member_product_list'), 1);
        }
    }

    public function member_product_del() {
        $p = I('p');
        $rst = M('member_product')->where(array('id' => I('id')))->delete();
        if ($rst !== false) {
            $this->success('数据删除成功', U('member_product_list', array('p' => $p)), 1);
        } else {
            $this->error('数据删除失败', U('member_product_list', array('p' => $p)), 0);
        }
    }

    public function remark() {
        $data = I("post.");
        $res = M("member_product")->where(array('id' => $data['id']))->save($data);
        if ($res !== false) {
            $this->ajaxReturn(true);
        }
        $this->ajaxReturn(false);
    }

    public function member_product_export() {
        // 筛选客户
        $map['member_list_addtime'] = array("egt", strtotime(I("start_time") . " 00:00:00"));
        $map['member_list_addtime'] = array("elt", strtotime(I("end_time") . " 23:59:59"));
        $member_products = M("member_product")->order('addtime asc')->select();
        foreach ($member_products as $key => $member_product) {
            $datas[$key]['member_name'] = getField($member_product['member_id'], 'member_list', 'member_list_name', 'member_list_id');
            $datas[$key]['member_tel'] = getField($member_product['member_id'], 'member_list', 'member_list_tel', 'member_list_id');
            $datas[$key]['product_name'] = getField($member_product['product_id'], 'product_list', 'product_list_name', 'product_list_id');
            $datas[$key]['start_time'] = date("Y-m-d", $member_product['start_time']);
            $datas[$key]['end_time'] = date("Y-m-d", $member_product['end_time']);
            $datas[$key]['admin_name'] = getField($member_product['admin_id'], 'admin', 'admin_realname', 'admin_id');
            $datas[$key]['addtime'] = date("Y-m-d H:i", $member_product['addtime']);
        }

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
        $titles = array("客户姓名", "客户联系方式", "购买产品", "产品开始时间", "产品结束时间", "负责人", "签单时间");
        foreach ($titles as $i => $name) {
            $ck = num2alpha($i++) . '1';
            $active->setCellValue($ck, $name);
        }
        $fields = array("member_name", "member_tel", "product_name", "start_time", "end_time", "admin_name", "addtime");
        //填充数据
        foreach ($datas as $k => $v) {
            $k = $k + 1;
            $num = $k + 1; //数据从第二行开始录入
            $objPHPExcel->setActiveSheetIndex(0);
            foreach ($fields as $i => $name) {
                $ck = num2alpha($i++) . $num;
                $active->setCellValue($ck, $v[$name]);
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle("客户签单表");
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . "客户签单表" . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function ajaxGetMembers() {
        $query = urldecode(I("query"));
        $map["member_list_name"] = array("like", "%" . $query . "%");
        $datas = M("member_list")->where($map)->select();
        foreach ($datas as $key => $val) {
            $return[$key]['id'] = $val['member_list_id'];
            $return[$key]['label'] = $val['member_list_name'] . "(" . $val['member_list_tel'] . ")";
        }
        $this->ajaxReturn($return);
    }

    public function ajaxGetAdmins() {
        $query = urldecode(I("query"));
        $map["admin_realname|admin_tel"] = array("like", "%" . $query . "%");
        $datas = M("admin")->where($map)->select();
        foreach ($datas as $key => $val) {
            $return[$key]['id'] = $val['admin_id'];
            $return[$key]['label'] = $val['admin_realname'] . "(" . $val['admin_tel'] . ")";
        }
        $this->ajaxReturn($return);
    }

    public function member_assign() {
        $map["member_list_id"] = array("in", I("ids"));
        $res = M("member_list")->where($map)->setField("admin_id", I('admin_id'));

        if ($res !== false) {
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
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
