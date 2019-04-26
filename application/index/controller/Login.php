<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Session;
class Login extends Base
{
    /**
     * 登录
     */
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
            $checkData['sms_type'] = $data['sms_type'];
            $checkData['code'] = $data['code'];
            $checkData['phone'] = $data['mobile'];            
            $res = checkPhoneCode($checkData);
            if($res['code']==0){
                // return array('code' => 0, 'msg' => $res['msg']);
                return json(['code'=>0,'msg'=> $res['msg']]);
            }
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
    public function login_commit()
    {
        $data = input('post.'); 

        if($_POST){
            // 验证

            if($data['check'] == 1){
                $val = 'mobile';
                if (empty($data['mobile'])) {
                    return array('code' => 0, 'msg' => '请输入手机号');
                }
                $check_phone = check_mobile_number($data['mobile']);
                if (!$check_phone) {
                    return array('code' => 0, 'msg' => '手机号格式不正确');
                }                
                if(!$data['code']){
                    return array('code' => 0, 'msg' => '请输入验证码');
                }
                // 验证码
                // $checkData['sms_type'] = $data['sms_type'];
                // $checkData['code'] = $data['code'];
                // $checkData['phone'] = $data['mobile'];
                // $res = checkPhoneCode($checkData);
                // if ($res['code'] == 0) {
                //     return json(['code' => 0, 'msg' => $res['msg']]);
                // }
            }else{
                $val = 'mobile1';
                $LoginValidate = Loader::Validate('Login');
                if (!$LoginValidate->check($data)) {
                    $baocuo = $LoginValidate->getError();
                    return json(['code' => 0, 'msg' => $baocuo]);
                }
            }
            $check = Db::name('users')->where('mobile',$data[$val])->find();
            if($check){
                // 检查游戏剩余时间
                if ($check['end_time'] < time()) {
                    return array('code' => 0, 'msg' => '您的游戏时长不足，请充值之后再登录');
                }
                if( $data['check'] == 2 && ($check['password'] != md5($data['password']))){
                    return array('code' => 0, 'msg' => '密码错误');  
                }  
                Session::set('user',$check);
                return json(['code'=>1,'msg'=>'登录成功','url'=> '/index/index/index']);                      
            }else{
                return array('code' => 0, 'msg' => '此用户不存在'); 
            }
     
        }    
    }
    
}
