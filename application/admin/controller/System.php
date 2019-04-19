<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 21:35
 */
namespace app\admin\controller;

use think\Db;

class System extends Base
{
    private $menu_list;//基本设置菜单

    public function _initialize()
    {
        parent::_initialize();

        //配置列表
        $this->menu_list = array(
            'base_setting' => ['order'=>0,'url'=>'base_setting','name'=>"基本设置"],
//            'safe_setting' => ['order'=>1,'url'=>'safe_setting','name'=>"安全设置"],
            'email_setting'=> ['order'=>2,'url'=>'email_setting','name'=>"邮箱设置"],
            'other_setting'=> ['order'=>3,'url'=>'other_setting','name'=>"其他设置"]
        );

        $this->assign('menu_list',$this->menu_list);


    }

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
        foreach($info as $v){
            $data[$v['name']] = $v['value'];
        }
//        dump($data);//die;
        $index = $this->menu_list;
        $this->assign('index',$index['base_setting']['order']);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function safe_setting()
    {
        $type = "safe_setting";
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
        foreach($info as $v){
            $data[$v['name']] = $v['value'];
        }

        $this->assign('data',$data);
        $this->assign('index',1);
        return $this->fetch();
    }

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
        foreach($info as $v){
            $data[$v['name']] = $v['value'];
        }

        $this->assign('data',$data);
        $this->assign('index',2);
        return $this->fetch();
    }

    public function other_setting()
    {
        $type = "other_setting";
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
        foreach($info as $v){
            $data[$v['name']] = $v['value'];
        }

        $this->assign('data',$data);
        $this->assign('index',3);
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