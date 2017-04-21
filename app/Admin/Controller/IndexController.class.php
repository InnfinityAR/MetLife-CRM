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

class IndexController extends AuthController {

    public function index() {
        //未登录
        if (empty($_SESSION['aid'])) {
            $this->redirect('Login/login');
        }
        
        //查询：时间格式过滤
        $sldate = I('reservation', ''); //获取格式 2015-11-12 - 2015-11-18
        $arr = explode(" - ", $sldate); //转换成数组

        if (count($arr) == 2) {
            $arrdateone = strtotime($arr[0]);
            $arrdatetwo = strtotime($arr[1] . ' 23:59:59');
        }else{
            $arrdateone = mktime(0,0,0,date('m'),1,date('y'));
            $arrdatetwo = mktime(23,59,59,date('m'),date('t'),date('y'));
        }
        //获取x轴
        $xAixs = getXAixs($arrdateone, $arrdatetwo);
        // 判断是普通成员还是管理员
        $auth_map["uid"] = session("aid");
        $group_id = M("auth_group_access")->where($auth_map)->getField("group_id");

        if ($group_id == 2||$group_id == 1) {  // 管理员
            $auth = "manager";
            // 所有人的总销售金额数据
            $total_sale_data = getTotalSaleData($xAixs);
            $single_sale_data = getSingleSaleData($xAixs);
        } elseif ($group_id == 3) {      // 普通人员
            $auth = "common";
            $day_map['admin_id'] = session('aid');
            $week_map['admin_id'] = session('aid');
            $month_map['admin_id'] = session('aid');
            
            $total_sale_data = getTotalSaleData($xAixs, session('aid'));
            $single_sale_data = getSingleSaleData($xAixs,session('aid'));
        }
        foreach ($xAixs as $xAix){
            $array[] = trim($xAix);
        }
        $this->assign("total_sale_data", $total_sale_data);
        $this->assign("single_sale_data", $single_sale_data);
        $this->assign("xAixs", json_encode($array));
        $this->assign('sldate', $sldate);

        // 今日的成交量和成交金额
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $day_map["addtime"] = array(array("egt", $beginToday), array("elt", $beginToday), "AND");
        $day_price = M("member_product")->where($day_map)->sum("total_price");
        $day_count = M("member_product")->where($day_map)->count();
        $this->assign("day_price", $day_price);
        $this->assign("day_count", $day_count);

        // 本周的成交量和成交金额
        $beginLastweek = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
        $endLastweek = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y'));
        $week_map["addtime"] = array(array("egt", $beginLastweek), array("elt", $endLastweek), "AND");
        $week_price = M("member_product")->where($week_map)->sum("total_price");
        $week_count = M("member_product")->where($week_map)->count();
        $this->assign("week_price", $week_price);
        $this->assign("week_count", $week_count);

        // 本月的成交量和成交金额
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));

        $month_map["addtime"] = array(array("egt", $beginThismonth), array("elt", $endThismonth), "AND");
        $month_price = M("member_product")->where($month_map)->sum("total_price");
        $month_count = M("member_product")->where($month_map)->count();
        $this->assign("month_price", $month_price);
        $this->assign("month_count", $month_count);

        

        // 获取每个队员的销售业绩
        // 获取所有普通成员
        $common_map['addtime'] = array(array('egt', $arrdateone), array('elt', $arrdatetwo), 'AND');
        $admin_ids = M("auth_group_access")->where(array("group_id" => 3))->getField("uid", true);
        foreach ($admin_ids as $admin_id) {
            // 获取普通成员姓名
            $names[] = M("admin")->where(array("admin_id" => $admin_id))->getField("admin_realname");
            // 获取对应成交金额
            $common_map["admin_id"] = $admin_id;
            $total_price[] = M("member_product")->where($common_map)->sum("total_price");
        }
        $this->assign("total_price", json_encode($total_price));
        $this->assign("names", json_encode($names));
        $this->assign("auth", $auth);
        
        
        // 获取图标标题
        $title = getTitle($type);
        $this->assign("auth", $auth);
        $this->assign("title", $title);
        $this->assign("type", $type);


        //安全检测
        $this->system_safe = true;

        $this->danger_mode_debug = APP_DEBUG;
        if ($this->danger_mode_debug) {
            $this->system_safe = false;
        }

        $this->weak_setting_db_password = false;
        $weak_pwd_reg = array(
            '/^[0-9]{0,6}$/',
            '/^[a-z]{0,6}$/',
            '/^[A-Z]{0,6}$/'
        );
        foreach ($weak_pwd_reg as $reg) {
            if (preg_match($reg, C('DB_PWD'))) {
                $this->weak_setting_db_password = true;
                break;
            }
        }
        if ($this->weak_setting_db_password) {
            $this->system_safe = false;
        }
        $this->weak_setting_admin_password = session('admin_weak_pwd');
        if ($this->weak_setting_admin_password) {
            $this->system_safe = false;
        }
        $this->weak_setting_admin_last_change_password = (session('admin_last_change_pwd_time') < time() - 3600 * 24 * 30);
        if ($this->weak_setting_admin_last_change_password) {
            $this->system_safe = false;
        }
        $this->assign('system_pageshow', C('SHOW_PAGE_TRACE'));
        $debug = APP_DEBUG;
        $this->assign('debug', $debug);
        $log_size = 0;
        $log_file_cnt = 0;
        foreach (list_file(LOG_PATH) as $f) {
            if ($f ['isDir']) {
                foreach (list_file($f ['pathname'] . '/', '*.log') as $ff) {
                    if ($ff ['isFile']) {
                        $log_size += $ff ['size'];
                        $log_file_cnt++;
                    }
                }
            }
        }
        $this->assign('log_size', $log_size);
        $this->assign('log_file_cnt', $log_file_cnt);
        //版本检查
        $version = F('ver_last');
        if (empty($version)) {
            $version = checkVersion();
            F('ver_last', $version);
        }
        $ver_curr = substr(C('YFCMF_VERSION'), 1);
        $ver_last = substr($version, 1);
        if (version_compare($ver_curr, $ver_last) === -1) {
            $ver_str = '最新版本V' . $ver_last;
        } else {
            $ver_str = '已经是最新版本';
            $ver_last = '';
        }
        $this->assign('ver_str', $ver_str);
        $this->assign('ver_last', $ver_last);
        //渲染模板
        $this->display();
    }

}
