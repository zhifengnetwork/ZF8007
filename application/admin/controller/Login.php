<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/22 0022
 * Time: 17:57
 */
namespace app\admin\controller;

use think\Db;
use think\Session;
//use think\Cookie;
use think\captcha\Captcha;

class Login extends Base
{
    public function _initialize()
    {
        parent::_initialize();

        $admin_name = session::get('admin_name');
        if(!empty($admin_name)){
            $url = "http://".$_SERVER['HTTP_HOST']. "/index.php/admin";
            header("refresh:1;url=$url");
            exit;
        }

    }

    public function index()
    {
        return $this->fetch();
    }

    public function entry()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    public function login()
    {
//        die;
        $data = input('post.');

        //判断是否有此用户
        $is_name = Db::name('admin')->where('name',$data['username'])->find();
        if($is_name){
            //判断该管理员是否被锁定
            $res = Db::name('admin')->where('name',$data['username'])->find();
            if($res['is_lock'] == 1) {
                return json(['status' => -1, 'msg' => '该管理员已被禁用']);
            }
            //判断密码是否相等
            $password = pwd_encryption($data['password']);
            if ($res['password'] === $password){
                Session::set('admin_name',$data['username']);
                $admin_user = Db::name('admin')->where('name',$data['username'])->find();
                Session::set('admin_id',$admin_user['id']);
                //插入日志
                $action = 'sign';
                $desc = '登录';
                $log = $this->adminLog($action,$desc);

                return json(['status'=>1,'msg'=>'登录成功']);
            }else {
                return json(['status'=>-1,'msg'=>'用户名或密码错误，请重新登录！']);
            }
        }else {
            return json(['status'=>-1,'msg'=>'用户名或密码错误，请重新登录！']);
        }
    }

    public function adminLog($action,$desc)
    {
        $add['addtime'] = time();
        $add['admin_id'] = session('admin_id');
        $add['action'] = $action;
        $add['desc']   = $desc;
        // $add['log_ip'] = request()->ip();
        // $add['log_url'] = request()->baseUrl();
        Db::name('admin_log')->insert($add);
        return true;
    }

}