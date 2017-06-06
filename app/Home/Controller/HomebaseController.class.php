<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rainfer.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rainfer <81818832@qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\CommonController;
class HomebaseController extends CommonController{
	protected $user_model;
	protected $user;
	protected $userid;
	protected $yf_theme_path;
	protected function _initialize(){
            parent::_initialize();
            if(!is_login()){
                redirect("/Home/Login");
            }
            $this->controller = CONTROLLER_NAME;
            $this->action = ACTION_NAME;
		
	}
	/**
	 * 检查用户登录
	 */
	protected function check_login(){
		if(!session('hid')){
			$this->error('您还没有登录！',__ROOT__."/");
		}
	}
    /**
     * 检查操作频率
     * @param int $t_check 距离最后一次操作的时长
     */
    protected function check_last_action($t_check){
    	$action=MODULE_NAME."-".CONTROLLER_NAME."-".ACTION_NAME;
    	$time=time();
    	if(!empty($_SESSION['last_action']['action']) && $action==$_SESSION['last_action']['action']){
    		$t=$time-$_SESSION['last_action']['time'];
    		if($t_check>$t){
    			$this->error("操作太频繁，请喝杯咖啡后再试!",0,0);
    		}else{
    			$_SESSION['last_action']['time']=$time;
    		}
    	}else{
    		$_SESSION['last_action']['action']=$action;
    		$_SESSION['last_action']['time']=$time;
    	}
    }
}