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

class MessageController extends AuthController {

    // 消息展示
    public function index() {
        // 获取所有的普通管理员
        $admin_ids = M("auth_group_access")->where(array("group_id" => 3))->getField("uid", true);
        foreach ($admin_ids as $key => $admin_id) {
            $admins[$key] = M("admin")->find($admin_id);
            $map["admin_id"] = $admin_id;
            $clients = M("member_list")->where($map)->select();
            for ($i = 0; $i <= 30; $i++) {
                foreach ($clients as $client) {
                
                    $start = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + $i * 3600 * 24;
                    $end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1 + $i * 3600 * 24;
                    
                    if(date("m")==12&& date("m",$client["member_list_birth"])==1){
                        $birth = strtotime((date("Y")+1)."-".date("m-d H:i:s",$client["member_list_birth"]));
                    }else{
                        $birth = strtotime(date("Y")."-".date("m-d H:i:s",$client["member_list_birth"]));
                    }
                    if($birth<=$end&&$birth>=$start){
                        $admins[$key]["msgs"][] = "客户".$client['member_list_name']."将在".date("m-d",$client["member_list_birth"])."过生日";
                    }
                }
            }
        }
//        foreach ($admin_ids as $key=>$admin_id){
//            $admins[$key]["admin"] = M("admin")->find($admin_id);
//            $map["admin_id"] = $admin_id;
//            $clients = M("member_list")->where($map)->select();
//            
//            foreach ($clients as $client) {
//                if (date("m", time()) == 12 && date("m", $client["member_list_birth"]) == 1) {
//                    $end = (date("Y", time()) + 1) . "-" . date("m-d", $client["member_list_birth"]);
//                } else {
//                    $end = date("Y", time()) . "-" . date("m-d", $client["member_list_birth"]);
//                }
//
//                if(strtotime($end)- time()<3600*24*30&& strtotime($end)> time()){
//                    $births[]["msg"] = "客户".$client["member_list_name"]."将在".date("m-d",$client["member_list_birth"])."过生日";
//
//                }
//            }
//            $admins[$key]["births"] = $births;
//            unset($births);
//            
//        }
        $this->admins = $admins;
        $this->display();
    }

}
