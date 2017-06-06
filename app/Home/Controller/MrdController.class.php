<?php

namespace Home\Controller;

use Think\Verify;
use Home\Controller\HomebaseController;

class MrdController extends HomebaseController {

    // op列表
    public function op() {
        if(I("searchQuery")){
            $member_map["member_list_name|member_list_tel"] = array("like","%".I("searchQuery","")."%");
            $this->searchQuery = I("searchQuery");
        }
        $map["type_id"] = 1;
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

    
    public function opAdd() {
        if (IS_POST) {
            $client_id = I("client_id", "", "intval");

            if ($client_id) {     // 客户已存在
                // 保存客户类别
                $type_map["client_id"] = $data["client_id"] = $client_id;
                $type_map["type_id"] = $data["type_id"] = 1;
                $data["addtime"] = time();
                
                if (M("member_type")->where($type_map)->find()) {
                    $res = true;
                } else {
                    $res = M("member_type")->add($data);
                }

                if ($res) {
                    $member_data["isFF"] = I("isFF");
                    $member_data["bugdet"] = I("bugdet");
                    $map["member_list_id"] = $client_id;
                    M("member_list")->where($map)->save($member_data);
                    $back["status"] = true;
                    $back["msg"] = "添加成功";
                } else {
                    $back["status"] = false;
                    $back["msg"] = "添加失败";
                }
            } else {      // 用户不存在
                $member_data["member_list_name"] = I("name");
                $member_data["member_list_sex"] = I("sex");
                $member_data["member_list_tel"] = I("tel");
                $member_data["member_list_birth"] = strtotime(I("birth"));
                $member_data["isFF"] = I("isFF");
                $member_data["bugdet"] = I("bugdet");
                $member_data["admin_id"] = session("user_id");
                $member_data["member_list_addtime"] = time();
                $client_id = M("member_list")->add($member_data);

                $data["client_id"] = $client_id;
                $data["type_id"] = 1;
                $data["addtime"] = time();
                $res = M("member_type")->add($data);
                if ($res) {
                    $back["status"] = true;
                    $back["msg"] = "添加成功";
                } else {
                    $back["status"] = false;
                    $back["msg"] = "添加失败";
                }
            }
            $this->ajaxReturn($back);
        } else {
            $this->display();
        }
    }

    public function pc() {
        if(I("searchQuery")){
            $member_map["member_list_name|member_list_tel"] = array("like","%".I("searchQuery",""));
            $this->searchQuery = I("searchQuery");
        }
        $map["type_id"] = 2;
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

    public function pcAdd() {
        if (IS_POST) {
            $client_id = I("client_id");
            $type_map["client_id"] = $data["client_id"] = $client_id;
            $type_map["type_id"] = $data["type_id"] = 2;
            $data["addtime"] = time();
            if (M("member_type")->where($type_map)->find()) {
                $res = true;
            } else {
                $res = M("member_type")->add($data);
            }
            
            if($res){
                $member_data["n"] = I("n");
                $member_data["amp"] = I("amp");
                $map["member_list_id"] = $client_id;
                M("member_list")->where($map)->save($member_data);
                $back["status"] = true;
                $back["msg"] = "添加成功";
            }else{
                $back["status"] = false;
                $back["msg"] = "添加失败";
            }
            $this->ajaxReturn($back);
            
        } else {
            $this->display();
        }
    }
    
    public function ref() {
        if(I("searchQuery")){
            $member_map["member_list_name|member_list_tel"] = array("like","%".I("searchQuery",""));
            $this->searchQuery = I("searchQuery");
        }
        $map["type_id"] = 3;
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
    
    public function refAdd() {
        if (IS_POST) {
            // 添加客户
            $member_data["member_list_name"] = I("name");
            $member_data["member_list_sex"] = I("sex");
            $member_data["member_list_tel"] = I("tel");
            $member_data["member_list_birth"] = strtotime(I("birth"));
            $member_data["admin_id"] = session("user_id");
            $member_data["member_list_addtime"] = time();
            $client_id = M("member_list")->add($member_data);
            
            $data["client_id"] = $client_id;
            $data["type_id"] = 3;
            $data["addtime"] = time();
            $res = M("member_type")->add($data);
            
            if ($res) {
                $back["status"] = true;
                $back["msg"] = "添加成功";
            } else {
                $back["status"] = false;
                $back["msg"] = "添加失败";
            }
            $this->ajaxReturn($back);
        } else {
            $this->display();
        }
    }

    // 模糊搜索客户
    public function ajaxGetClient() {
        $query = I("query");
        $map["member_list_name|member_list_tel"] = array("like", $query . "%");
        $map["admin_id"] = session("user_id");
        
        $clients = M("member_list")->where($map)->limit(0, 8)->select();
        foreach ($clients as $client) {
            $data[$client["member_list_id"]] = $client["member_list_name"] . "(" . $client["member_list_tel"] . ")";
        }
        $this->ajaxReturn($data);
    }

    public function ajaxGetDetail() {
        $client_id = I("client_id", "", "intval");

        $client = M("member_list")->find($client_id);
        $client["member_list_birth"] = date("Y-m-d", $client["member_list_birth"]);
        if ($client["member_list_sex"] == 1) {
            $client["member_list_sex"] = "男";
        } else {
            $client["member_list_sex"] = "女";
        }
        $this->ajaxReturn($client);
    }

}
