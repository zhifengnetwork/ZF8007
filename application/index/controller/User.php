<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/26 0026
 * Time: 15:23
 */
namespace app\index\controller;

header('content-type:text/html;charset=utf-8');

use think\Db;
use think\Session;
use think\Request;

class User extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

    }

    //个人中心首页
    public function mycenter()
    {
        $this->check_user();
        $info = Db::name('users')->where('id', $this->user_id)->find();
        $time = Session::get('time');
        dump($time);
        $this->assign('time',$time);
        $this->assign('info',$info);
        return $this->fetch();
    }

    //历史记录
    public function brokerage()
    {
        $user_id = SESSION_ID;//dump($user_id);die;
        $info = Db::name('users')->where('id',$user_id)->find();
        $team_ids = Db::name('users')->where('first_leader',$user_id)->field('id ,nickname, commission')->select();
        $count = count($team_ids);
        $this->assign('info',$info);
        $this->assign('team_ids',$team_ids);
        $this->assign('count',$count);
        return $this->fetch();
    }
    //公告
    public function notice()
    {
        $data = Db::name('article')->where('type','0')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //免责说明
    public function disclaimer()
    {
        $data = Db::name('article')->where('type','1')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //使用说明
    public function use_notice()
    {
        $data = Db::name('article')->where('type','2')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //邀请链接
    public function invitelink()
    {
        return $this->fetch();
    }

    //上传凭证
    public function uploadfile(Request $request)
    {
        if ($_POST){
            $data = $_POST;
            $file = $request->file();
            dump($file);die;
        }


        $pay_way = Db::name('config')->where('type','pay_setting')->select();
        $pay_way[0]['img'] = $pay_way[2]['value'];
        $pay_way[1]['img'] = $pay_way[3]['value'];

        $package = Db::name('package')->select();
        $this->assign('pay',$pay_way);
        $this->assign('package',$package);//dump($package);die;
        return $this->fetch();
    }
     /**
      * 用户设置 
      */ 
    public function setting(){
        return $this->fetch();
    }
    /**
     * 退出
     */
    public function logout(){
        if($_POST){
           $check = Session::get('user');
           if($check){ 
                Session::delete('user');           
                if (empty(Session::get('user'))) {
                    return json(['status' => 1, 'msg' => '退出成功！','url'=>'/index/login/index']);
                } else {
                    return json(['status' => 0, 'msg' => '退出失败，请稍后再试！']);
                }                
           }else{
               return json(['status' => 0, 'msg' => '退出异常，请稍后再试！']);
           }
        }
    }

    /**
     *检查用户是否登陆
     */      
    public function check_user(){
        $user = Session::get('user');
        if (empty($user)) {
            $url = "http://" . $_SERVER['HTTP_HOST'] . "/index/login/index";
            header("refresh:1;url=$url");
        }
    }
}