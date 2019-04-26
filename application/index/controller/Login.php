<?php
namespace app\index\controller;
use think\Controller;
use app\admin\controller\Base;
use think\Db;

class Login extends Base
{
    public function index(){
        return $this->fetch();
    }
    /**
     * 注册
     */
    public function register(){
        return $this->fetch();
    }
    
    public function getPhoneVerify(){
        $param = input('post.');
        $sms_type = intval($param['sms_type']);
        if(!$sms_type || !$param['phone']){
            return json(array('code' => 0, 'msg' => '缺少参数'));
        }
        $data = ['sms_type'=>$sms_type, 'phone'=>$param['phone']];
        $res = getPhoneCode($data);
        return json($res);
    }

    public function re_commit(){
        $data = input('post.');
        dump($data);
    } 
    
}
