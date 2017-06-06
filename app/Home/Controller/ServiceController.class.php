<?php

namespace Home\Controller;

use Think\Verify;
use Home\Controller\HomebaseController;

class ServiceController extends HomebaseController {

    // 客户生日提醒
    public function index() {
        $map["admin_id"] = session("user_id");
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
                    $births[]["msg"] = "客户".$client['member_list_name']."将在".date("m-d",$client["member_list_birth"])."过生日";
                }
//                if(strtotime($end)- time()<3600*24*30&& strtotime($end)> time()){
//                    $births[]["msg"] = "客户".$client["member_list_name"]."将在".date("m-d",$client["member_list_birth"])."过生日";
//
//                }
            }
        }
        $this->births = $births;
        $this->display();
    }

}
