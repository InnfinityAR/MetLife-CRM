<?php

namespace Home\Controller;

use \Think\Controller;

class LoginController extends Controller {

    // 登录首页
    public function index() {
        if (is_login()) {
            redirect("/");
        } else {
            $this->display();
        }
    }
    
    public function login() {
        $map["admin_tel|admin_username"] = I("tel");
        $password = I("password");
        $admin = M('admin')->where($map)->find();
        
        if (!$admin || encrypt_password($password, $admin['admin_pwd_salt']) !== $admin['admin_pwd']) {
            $back["status"] = false;
            $back["msg"] = "用户名或密码错误";
        } else {
            // 禁用
            if(!$admin["admin_open"]){
                $back["status"] = false;
                $back["msg"] = "您已被管理员禁止登陆";
            }else{
                session("user_id",$admin["admin_id"]);
                $back["status"] = true;
                $back["msg"] = "登陆成功";
            }
            
        }
        $this->ajaxReturn($back);
        
    }
    
    // 退出登录
    public function logout() {
        session("user_id",null);
        redirect("/");
    }

}
