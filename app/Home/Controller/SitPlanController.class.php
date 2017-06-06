<?php

namespace Home\Controller;

use Think\Verify;
use Home\Controller\HomebaseController;

class SitPlanController extends HomebaseController {

    // sitPlan列表
    public function index() {
        if(I("searchQuery")){
            $member_map["member_list_name|member_list_tel"] = array("like","%".I("searchQuery","")."%");
            $this->searchQuery = I("searchQuery");
        }
        $map["type_id"] = 4;
        $client_ids = M("member_type")->where($map)->getField("client_id", true);
        $client_ids = array_unique($client_ids);
        if($client_ids){
            $member_map["member_list_id"] = array("in", $client_ids);
            $member_map["admin_id"] = session("user_id");
            $clients = M("member_list")->where($member_map)->order("member_list_addtime desc")->select();

            $this->clients = $clients;
        }
        $this->display();
        
    }

    // 添加sitPlan
    public function add() {
        if (IS_POST) {
            $map["member_list_name"] = $data["member_list_name"] = I("name");
            $map["member_list_tel"] = $data["member_list_tel"] = I("tel");
            
            if(M("member_list")->where($map)->find()){  // 客户已存在
                $back["status"] = false;
                $back["msg"] = "该用户已存在";
            }else{      // 存入客户信息
                $data["member_list_sex"] = I("sex","","intval");
                $data["member_list_birth"] = strtotime(I("birth"));
                $data["member_list_addtime"] = time();
                $data["admin_id"] = session("user_id");
                
                $res = M("member_list")->add($data);
                if($res){
                    $type_data["type_id"] = 4;
                    $type_data["client_id"] = $res;
                    $type_data["addtime"] = time();
                    M("member_type")->add($type_data);
                    
                    $back["status"] = true;
                    $back["msg"] = "用户信息添加成功!";
                }else{
                    $back["status"] = false;
                    $back["msg"] = "用户信息添加失败!";
                }
            }
            $this->ajaxReturn($back);
        } else {
            $this->display();
        }
    }
    
    


}
