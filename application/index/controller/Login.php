<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Session;
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
        if($_POST){
            // 验证
            $RegisterValidate = Loader::Validate('Register');
            if(!$RegisterValidate->check($data)){
                $baocuo=$RegisterValidate->getError();
                return json(['code'=>0,'msg'=> $baocuo]);
            }
            // 验证码
            // $checkData['sms_type'] = $data['sms_type'];
            // $checkData['code'] = $data['code'];
            // $checkData['phone'] = $data['mobile'];            
            // $res = checkPhoneCode($checkData);
            // if($res['code']==0){
            //     // return array('code' => 0, 'msg' => $res['msg']);
            //     return json(['code'=>0,'msg'=> $res['msg']]);
            // }
            //注册成功，有一天时长 
            $e_time = strtotime(date('Y-m-d H:i:s', strtotime('+1 day')));
            $data1 = [
                'mobile'         => $data['mobile'],
                'password'       => md5($data['password']),
                'register_time'  => time(),
                'register_method'=> 'mobile',
                'end_time'       =>  $e_time
            ];
           $res = Db::name('users')->insertGetId($data1);
           if($res){
                $user_info = Db::name('users')->where('id',$res)->find();
                Session::set('user',$user_info);
                return json(['code'=>1,'msg'=>'注册成功','url'=> '/index/index/index']);
           }else{
                return json(['code' => 0, 'msg' => '注册失败']);
           }        
        }
    } 
    
}
