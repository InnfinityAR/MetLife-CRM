<?php

/**
 * 获取时间范围
 * @param int $type 时间范围类型 1代表本周 2本月 3上个月 4上3个月 5上6个月 6本年
 */
function getTimeRange($type) {
    switch ($type) {
        case 1:
            $time_range['start_time'] = mktime(0, 0, 0, date('m'), date('d') - date('w'), date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('y'));
            break;

        case 2:
            $time_range['start_time'] = mktime(0, 0, 0, date('m'), 1, date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, date('m'), date('t'), date('y'));
            break;

        case 3:
            $time_range['start_time'] = mktime(0, 0, 0, date('m') - 1, 1, date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, date('m') - 1, date('t'), date('y'));
            break;

        case 4:
            $time_range['start_time'] = mktime(0, 0, 0, date('m') - 3, 1, date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, date('m') - 1, date('t'), date('y'));
            break;

        case 5:
            $time_range['start_time'] = mktime(0, 0, 0, date('m') - 6, 1, date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, date('m') - 1, date('t'), date('y'));
            break;

        case 6:
            $time_range['start_time'] = mktime(0, 0, 0, 1, 1, date('y'));
            $time_range['end_time'] = mktime(23, 59, 59, 12, date('t'), date('y'));
            break;

        default:
            break;
    }
    return $time_range;
}

/**
 * 获取总销售额
 * @param int $type 时间范围类型 1代表本周 2本月 3上个月 4上3个月 5上6个月 6本年
 */
function getXAixs($start_time, $end_time) {
    $map['addtime'] = array(array('egt', $start_time), array('elt', $end_time), 'AND');
    $addtimes = M("member_product")->where($map)->order('addtime asc')->getField("addtime", true);
    foreach ($addtimes as $addtime) {
        $dates[] = date("m-d", $addtime);
    }
    $dates = array_unique($dates);
    return $dates;
}

function getTotalSaleData($dates, $admin_id = '') {
    if ($admin_id) {
        $map['admin_id'] = $admin_id;
    }
    $start_time = strtotime($dates[0]);
    foreach ($dates as $date) {
        $end_time = strtotime(date('y') . '-' . $date . ' 23:59:59');
        $map['addtime'] = array(array('egt', $start_time), array('elt', $end_time), 'AND');
        $total_sale_data[] = M("member_product")->where($map)->sum("total_price");
    }
    return json_encode($total_sale_data);
}

/**
 * 获取总销售额
 * @param int $type 时间范围类型 1代表本周 2本月 3上个月 4上3个月 5上6个月 6本年
 */
function getSingleSaleData($dates, $admin_id = '') {
    if ($admin_id) {
        $map['admin_id'] = $admin_id;
    }
    $start_time = strtotime($dates[0]);
    foreach ($dates as $date) {
        $start_time = strtotime(date('y') . '-' . $date . ' 00:00:00');
        $end_time = strtotime(date('y') . '-' . $date . ' 23:59:59');
        $map['addtime'] = array(array('egt', $start_time), array('elt', $end_time), 'AND');
        $single_sale_data[] = M("member_product")->where($map)->sum("total_price");
    }
    return json_encode($single_sale_data);
}

function getTitle($type) {
    switch ($type) {
        case 1:
            $title = "本周";
            break;

        case 2:
            $title = "本月";
            break;

        case 3:
            $title = "上个月";
            break;

        case 4:
            $title = "上3个月";
            break;

        case 5:
            $title = "上6个月";
            break;

        case 6:
            $title = "本年";
            break;

        default:
            break;
    }
    return $title;
}
