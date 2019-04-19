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

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub

        $global_menu_list = $this->menu();
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
//        dump($global_menu_list);die;

        return $global_menu_list;
    }
}