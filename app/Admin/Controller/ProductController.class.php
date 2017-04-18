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

class ProductController extends AuthController {
    /*
     * 用户管理
     */

    public function product_list() {
        $key = I('key');
        if ($key) {
            $map['product_list_name'] = array('like', "%" . $key . "%");
        }

        /*
         * 分页操作
         */
        $count = M('product_list')->where($map)->count(); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $product_list = M('product_list')->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order('product_list_addtime desc')->select();
        $this->assign('product_list', $product_list);
        $this->assign('page', $show);
        $this->assign('val', $key);
        $this->display();
    }

    /*
     * 添加用户界面
     */

    public function product_add() {

        $this->display();
    }

    /*
     * 添加用户操作
     */

    public function product_runadd() {
        if (!IS_AJAX) {
            $this->error('提交方式不正确', U('member_list'), 0);
        } else {
            $sl_data = array(
                'product_list_name' => I('product_list_name'),
                'product_list_price' => I('product_list_price'),
                'product_list_addtime' => time(),
            );
            $rst = M('product_list')->add($sl_data);
            if ($rst !== false) {
                $this->success('产品添加成功', U('product_list'), 1);
            } else {
                $this->error('产品添加失败', U('product_add'), 0);
            }
        }
    }

    /*
     * 修改用户信息界面
     */

    public function product_edit() {

        $product_list_edit = M('product_list')->where(array('product_list_id' => I('product_list_id')))->find();
        $this->assign('product_list_edit', $product_list_edit);

        $this->display();
    }

    /*
     * 修改用户操作
     */

    public function product_runedit() {
        if (!IS_AJAX) {
            $this->error('提交方式不正确', U('product_list'), 0);
        } else {

            $sl_data = I('post.');
            M('product_list')->save($sl_data);
            
            $this->success('产品修改成功', U('product_list'), 1);
        }
    }

    /*
     * 删除
     */

    public function product_del() {
        $p = I('p');
        $rst = M('product_list')->where(array('product_list_id' => I('product_list_id')))->delete();
        if ($rst !== false) {
            $this->success('产品删除成功', U('product_list', array('p' => $p)), 1);
        } else {
            $this->error('产品删除失败', U('product_list', array('p' => $p)), 0);
        }
    }

}
