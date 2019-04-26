<?php

/**
 * 公共文件
 */

 use think\Db;

 //分组名称
function group_name($group_id){
    $group_name = Db::name('user_group')->where('id',$group_id)->value('name');

    $group_name = $group_name ? $group_name : "暂无分组";
    return $group_name;
}

//上级名称
function first_leader($user_id){
    $name = Db::name('users')->where('first_leader',$user_id)->value('nickname');
    $name = $name ? $name : "无";
    return $name;
}

function adminLog($log_info)
{
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    // $add['log_ip'] = request()->ip();
    // $add['log_url'] = request()->baseUrl();
    M('admin_log')->add($add);
}

// 搜索条件
 function s_condition($data,$name)
{
    $time = " 23:59:59";
    if ($data['m_conditions']) {
        $m_conditions = str_replace(' ', '', $data['m_conditions']);
        $where[$name] = ['like', "%$m_conditions%"];
    }
    if ($data['datemin'] && $data['datemax']) {
        $datemin = strtotime($data['datemin']);
        $datemax = strtotime($data['datemax']);
        $where['pay_time'] = [['>= time', $datemin], ['<= time', $datemax], 'and'];
    } elseif ($data['datemin']) {
        $where['pay_time'] = ['>= time', strtotime($data['datemin'])];
    } elseif ($data['datemax']) {
        $where['pay_time'] = ['<= time', strtotime($data['datemax'])];
    }
    return $where;
}