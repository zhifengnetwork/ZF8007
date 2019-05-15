<?php

/**
 * 公共文件
 */

 use think\Db;
 use think\Session;
 use \think\Controller;

 //用户名
function user_name($user_id){
    $name = Db::name('users')->where('id',$user_id)->value('nickname');
    return $name;
}

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
//    $where['user_id'] = ['>', 0];
     $where = '';
    if ($data['m_conditions'] && !empty($data['m_conditions'])) {
        $m_conditions = str_replace(' ', '', $data['m_conditions']);
        $where[$name] = ['like', "%$m_conditions%"];
    }
    if (!empty($data['datemin']) && !empty($data['datemax'])) {
        $datemin = strtotime($data['datemin']);
        $datemax = strtotime($data['datemax']);
        $where['pay_time'] = [['>= time', $datemin], ['<= time', $datemax], 'and'];
    } elseif (!empty($data['datemin'])) {
        $where['pay_time'] = ['>= time', strtotime($data['datemin'])];
    } elseif (!empty($data['datemax'])) {
        $where['pay_time'] = ['<= time', strtotime($data['datemax'])];
    }
    return $where;
}

/**
 *检查用户是否登陆
 */
function check_user(){
    $admin = Session::get('admin_name');
//    dump($admin);die;
    if (empty($admin)) {
         $url = "http://" . $_SERVER['HTTP_HOST'] . "/admin/login/index";
//        header('/admin/login/index');
        header("refresh:1;url=$url");
        exit;
    }
}

/**
 * 获取后台管理员昵称
 */
function get_admin_name($id){
    if($id>0){
        return Db::name('admin')->where(['id'=>$id])->value('name');
    }
}

function get_withdraw_way($withdraw_id,$user_id)
{
    $value = Db::name('withdraw_way')
        ->where(['user_id'=>$user_id,'withdraw_way'=>$withdraw_id])
//        ->value('value');
        ->find();
//    dump($value['address']);//die;
    return $value;
}