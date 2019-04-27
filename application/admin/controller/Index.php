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
        return $this->fetch();
    }
    public function logout()
    {
        session::clear();
        $this->redirect('admin/login/index');
    }
}