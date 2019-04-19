<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 21:35
 */
namespace app\admin\controller;

class System extends Base
{
    private $menu_list;//基本设置菜单

    public function _initialize()
    {
        parent::_initialize();

        $this->menu_list = array(
            'base_setting' => ['order'=>0,'url'=>'base_setting','name'=>"基本设置"],
            'safe_setting' => ['order'=>1,'url'=>'safe_setting','name'=>"安全设置"],
            'email_setting'=> ['order'=>1,'url'=>'email_setting','name'=>"邮箱设置"],
            'other_setting'=> ['order'=>2,'url'=>'other_setting','name'=>"其他设置"]
        );

        $this->assign('menu_list',$this->menu_list);


    }

    public function base_setting()
    {
        $type = "base_setting";
        $data = input('post.');
        dump($data);//die;

        $index = $this->menu_list;
        $this->assign('index',$index);
        return $this->fetch();
    }

    public function safe_setting()
    {
        $data = input('post.');
        dump($data);//die;
        return $this->fetch();
    }

    public function email_setting()
    {
        $data = input('post.');
        dump($data);//die;
        return $this->fetch();
    }

    public function other_setting()
    {
        $data = input('post.');
        dump($data);//die;
        return $this->fetch();
    }

    public function category()
    {
        return $this->fetch();
    }

    public function category_add()
    {
        return $this->fetch();
    }

    public function data()
    {
        return $this->fetch();
    }

    public function shielding()
    {
        return $this->fetch();
    }

    public function log()
    {
        return $this->fetch();
    }

}