<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 17:32
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
/**
 * Class Index
 * 后台管理系统首页
 * @package app\admin\controller
 */
class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize();

        check_user();
    }


    public function index()
    {
        dump(session('admin_id'));//die;
        return $this->fetch();
    }

    public function welcome()
    {
        $data = Db::name('admin_log')->where('admin_id',Session::get('admin_id'))->order('addtime desc')->select();
        $count = count($data);
        $info = Db::name('admin')->where('id',Session::get('admin_id'))->find();

        $server = $_SERVER;
//        dump($server);die;
        $this->assign('data',$data);
        $this->assign('info',$info);
        $this->assign('count',$count);
        $this->assign('server',$server);
        return $this->fetch();
    }

    public function logout()
    {
        session::clear();
        $this->redirect('admin/login/index');
    }
}