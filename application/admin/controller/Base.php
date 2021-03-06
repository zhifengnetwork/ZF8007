<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 20:02
 */
namespace app\admin\controller;

use think\Db;
use think\Controller;
use think\Session;

class Base extends Controller
{
    public $admin;
    public $admin_id;

    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub

        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.tp-shop.cn/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        if (Session::has('user')) {
            $this->admin = Session::get('admin_name');
            $this->admin_id = Session::get('admin_id');
        }


        $global_menu_list = $this->menu();
        $global_admin = $this->header();
        $this->assign('global_admin',$global_admin);
        $this->assign('global_menu_list',$global_menu_list);

    }

    public function menu()
    {
        $global_menu_list = Db::query('select `id`,`name`,`icon` from `zf_menu` where `is_lock`=0 and `parent_id` = 0');
        if($global_menu_list){
            foreach($global_menu_list as $k => $v){
                $global_menu_list[$k]['last'] = Db::query("select `id`,`name`,`url` from `zf_menu` where `is_lock` = 0 and `parent_id` = '$v[id]'");
            }
        }

        return $global_menu_list;
    }

    public function ajaxReturn($data,$type = 'json'){
        exit(json_encode($data));
    }

    public function header()
    {
        $admin_id = Session::get('admin_id');
        $global_admin = Db::name('admin')->where('id',$admin_id)->find();
        return $global_admin;
    }
}