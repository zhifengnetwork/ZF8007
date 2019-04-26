<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23 0023
 * Time: 14:51
 */

namespace app\index\controller;


use think\Controller;
use think\Db;

class Base extends Controller
{
    public $session_id;
    public $cateTrre = array();
    /*
     * 初始化操作
     */
    public function _initialize() {
        if (input("unique_id")) {           // 兼容手机app
            session_id(input("unique_id"));
            Session::start();
        }
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.tp-shop.cn/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用

        // 判断当前用户是否手机
//        if(isMobile())
//            cookie('is_mobile','1',3600);
//        else
//            cookie('is_mobile','0',3600);

    }

    /*
     *
     */
    public function ajaxReturn($data)
    {
        
        exit(json_encode($data));
    }
}