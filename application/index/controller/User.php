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

class User extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

    }

    //个人中心首页
    public function mycenter()
    {
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
    public function uploadfile()
    {
        return $this->fetch();
    }
}