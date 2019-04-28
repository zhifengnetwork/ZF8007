<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 21:35
 */
namespace app\admin\controller;

header('content-type:text/html;charset=utf-8');

use think\Db;
use think\Request;

class System extends Base
{
    private $menu_list;//基本设置菜单

    public function _initialize()
    {
        parent::_initialize();

        check_user();
        //配置列表
        $this->menu_list = array(
            'base_setting' => ['order'=>0,'url'=>'base_setting','name'=>"基本设置"],
//            'safe_setting' => ['order'=>1,'url'=>'safe_setting','name'=>"安全设置"],
            'email_setting'=> ['order'=>1,'url'=>'email_setting','name'=>"邮箱设置"],
            'pay_setting'=> ['order'=>2,'url'=>'pay_setting','name'=>"支付设置"],
//            'other_setting'=> ['order'=>2,'url'=>'other_setting','name'=>"其他设置"]
        );

        $this->assign('menu_list',$this->menu_list);


    }

    //基础设置
    public function base_setting()
    {
        $type = "base_setting";
        if($_POST){
            $info = input('post.');
            foreach($info as $k=>$v){
                if(Db::name('config')->where(['type'=>$type,'name'=>$k])->find()){
                    Db::name('config')->where(['type'=>$type,'name'=>$k])->update(['value'=>$v]);
                }else{
                    Db::name('config')->insert(['name'=>$k,'value'=>$v,'type'=>$type]);
                }
            }
        }

        $info = Db::name('config')->where('type',$type)->select();
        if ($info){
            foreach($info as $v){
                $data[$v['name']] = $v['value'];
            }
            $this->assign('data',$data);
        }
        $index = $this->menu_list;
        $this->assign('index',$index['base_setting']['order']);
        return $this->fetch();
    }

    // public function safe_setting()
    // {
    //     $type = "safe_setting";
    //     if($_POST){
    //         $info = input('post.');
    //         foreach($info as $k=>$v){
    //             if(Db::name('config')->where(['type'=>$type,'name'=>$k])->find()){
    //                 Db::name('config')->where(['type'=>$type,'name'=>$k])->update(['value'=>$v]);
    //             }else{
    //                 Db::name('config')->insert(['name'=>$k,'value'=>$v,'type'=>$type]);
    //             }
    //         }
    //     }

    //     $info = Db::name('config')->where('type',$type)->select();
    //     foreach($info as $v){
    //         $data[$v['name']] = $v['value'];
    //     }

    //     $this->assign('data',$data);
    //     $this->assign('index',1);
    //     return $this->fetch();
    // }

    //邮箱设置
    public function email_setting()
    {
        $type = "email_setting";
        if($_POST){
            $info = input('post.');
            foreach($info as $k=>$v){
                if(Db::name('config')->where(['type'=>$type,'name'=>$k])->find()){
                    Db::name('config')->where(['type'=>$type,'name'=>$k])->update(['value'=>$v]);
                }else{
                    Db::name('config')->insert(['name'=>$k,'value'=>$v,'type'=>$type]);
                }
            }
        }

        $info = Db::name('config')->where('type',$type)->select();
        if($info){
            foreach($info as $v){
                $data[$v['name']] = $v['value'];
            }
            $this->assign('data',$data);
        }
        $this->assign('index',1);
        return $this->fetch();
    }

    //支付设置
    public function pay_setting(Request $request)
    {
        $type = "pay_setting";
        if($_POST){
            $info = input('post.');
            $files = $request->file();
            if($files){
//            $img_name = md5(mt_rand(0,100000).time()); //文件名
                $img_path = 'public/upload/paycode/';
                if(!is_dir(ROOT_PATH.$img_path)){
                    mkdir(ROOT_PATH.$img_path,0777,true);
                }

                if(isset($files['file_1'])){
                    $ali = $files['file_1']->getInfo();
                    $ali['name'] = 'ali_code.png';
                    $ali_code = '/'.$img_path.$ali['name'];//dump($info);die;
                    move_uploaded_file($ali['tmp_name'], ROOT_PATH . '/' . $img_path . $ali['name']);
                    Db::name('config')->where(['type'=>$type,'name'=>'ali_code'])->update(['value'=>$ali_code]);
                };
                if(isset($files['file_2'])) {
                    $wechat = $files['file_2']->getInfo();
                    $wechat['name'] = 'wechat_code.png';
                    $wechat_code = '/'.$img_path.$wechat['name'];
                    move_uploaded_file($wechat['tmp_name'], ROOT_PATH . '/' . $img_path . $wechat['name']);
                    Db::name('config')->where(['type'=>$type,'name'=>'wechat_code'])->update(['value'=>$wechat_code]);
                }
//                $info['ali_code'] = '/'.$img_path.$ali['name'];
//                $info['wechat_code'] = '/'.$img_path.$wechat['name'];

            }
//            foreach($info as $k=>$v){
//                if(Db::name('config')->where(['type'=>$type,'name'=>$k])->find()){
//                    Db::name('config')->where(['type'=>$type,'name'=>$k])->update(['value'=>$v]);
//                }else{
//                    Db::name('config')->insert(['name'=>$k,'value'=>$v,'type'=>$type]);
//                }
//            }
        }

        $info = Db::name('config')->where('type',$type)->select();
        if ($info){
            foreach($info as $v){
                $data[$v['name']] = $v['value'];
            }
            $this->assign('data',$data);
        }
        $this->assign('index',2);
        return $this->fetch();
    }

    //其他设置
//    public function other_setting()
//    {
//        $type = "other_setting";
//        if($_POST){
//            $info = input('post.');
//            foreach($info as $k=>$v){
//                if(Db::name('config')->where(['type'=>$type,'name'=>$k])->find()){
//                    Db::name('config')->where(['type'=>$type,'name'=>$k])->update(['value'=>$v]);
//                }else{
//                    Db::name('config')->insert(['name'=>$k,'value'=>$v,'type'=>$type]);
//                }
//            }
//        }
//
//        $info = Db::name('config')->where('type',$type)->select();
//        foreach($info as $v){
//            $data[$v['name']] = $v['value'];
//        }
//
//        $this->assign('data',$data);
//        $this->assign('index',2);
//        return $this->fetch();
//    }

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