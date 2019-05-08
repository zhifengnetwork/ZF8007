<?php
namespace app\index\controller;
use think\Db;
use think\Loader;
use think\Session;
class Login extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        // $user = Session::get('user');
        // if (!empty($user)) {
        //     $url = "http://" . $_SERVER['HTTP_HOST'] . "/index/index/index";
        //     header("refresh:1;url=$url");
        // }        
    }    
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
        $user_id = input('get.id');
        $this->assign('user_id',$user_id); 
        return $this->fetch();
    }

    /**
     * 找回密码
     */
    public function find_pwd(){
        return $this->fetch();
    }
    /**
     * 获取验证码
     */
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
    /**
     * 注册逻辑
     */
    public function re_commit(){
        $data = input('post.');
        if($_POST){
            // 验证
            $check_reg = $this->check_register($data);
            if($check_reg) return $check_reg;
            //注册成功，有一天时长 
            $e_time = strtotime(date('Y-m-d H:i:s', strtotime('+1 day')));
            // $e_time = 86400;
            $data1 = [
                'mobile'         => $data['mobile'],
                'nickname'       => $data['mobile'],
                'password'       => pwd_encryption($data['password']),
                'register_time'  => time(),
                'register_method'=> 'mobile',
                'end_time'       =>  $e_time
            ];
            if($data['user_id']){
                $has_user = Db::name('users')->where('id',$data['user_id'])->find(); 
                if($has_user){
                   $data1['first_leader'] = $data['user_id'];
                }
            }              
            $res = Db::name('users')->insertGetId($data1);
            if($res){
                $user_info = Db::name('users')->where('id',$res)->find();
                Session::set('user',$user_info);
                Session::set('time', time());
                return json(['code'=>1,'msg'=>'注册成功','url'=> '/index/index/index']);
            }else{
                return json(['code' => 0, 'msg' => '注册失败']);
            }        
        }
    }
    /**
     * 登录逻辑
     */
    public function login_commit()
    {
        $data = input('post.'); 
        if($_POST){
            // 验证

            if($data['check'] == 1){
                $val = 'mobile';
                $err = $this->check_mobile($data);
                if ($err) return $err;
            }else{
                $val = 'mobile1';
                $LoginValidate = Loader::Validate('Login');
                if (! $LoginValidate->scene( 'login_commit')->check($data)) {
                    $baocuo = $LoginValidate->getError();
                    return json(['code' => 0, 'msg' => $baocuo]);
                }
            }
            // 检查用户是否存在
            $check = Db::name('users')->where('mobile',$data[$val])->find();
            if($check){
                // // 检查游戏剩余时间
                // if ($check['end_time'] < time()) {
                //     return array('code' => 0, 'msg' => '您的游戏时长不足，请充值之后再登录');
                // }
                if( $data['check'] == 2 && ($check['password'] != pwd_encryption($data['password']))){
                    return array('code' => 0, 'msg' => '密码错误');  
                }  
                Session::set('user',$check);
                Session::set('time',time());
                return json(['code'=>1,'msg'=>'登录成功','url'=> '/index/index/index']);                      
            }else{
                return array('code' => 0, 'msg' => '此用户不存在'); 
            }
     
        }    
    }

    /**
     * 找回密码逻辑
     */
    public function find_commit(){
        $data = input('post.');
        if($_POST){
            //    验证
           if($data['status'] == 1){
                $err = $this->check_mobile($data);
                if ($err) return $err;
                $info = Db::name('users')->where('mobile', $data['mobile'])->find();

                if ($info) {
                    return json(['code' => 1, 'msg' => '查询成功','mobile'=>$data['mobile']]);
                } else {
                    return json(['code' => 0, 'msg' => '手机号不存在']);
                }
           }else{
                $LoginValidate = Loader::Validate('Login');
                if (!$LoginValidate->scene('find_commit')->check($data)) {
                    $baocuo = $LoginValidate->getError();
                    return json(['code' => 0, 'msg' => $baocuo]);
                }
                if($data['password'] != $data['re_password']){
                    return json(['code' => 0, 'msg' => '两次密码输入不一致']);
                }
                $where = [
                    'password' => pwd_encryption($data['password'])
                ];
                $res = Db::name('users')->where('mobile',$data['mobile1'])->update($where);
                if($res){
                    return json(['code' => 1, 'msg' => '密码修改成功']);
                
                }else{
                    return json(['code' => 0, 'msg' => '新密码和原密码一致']);
                }                
           }
 
           
        }        
    }

    public function check_mobile($data){
       
        if (empty($data['mobile'])) {
            return array('code' => 0, 'msg' => '请输入手机号');
        }
        $check_phone = check_mobile_number($data['mobile']);
        if (!$check_phone) {
            return array('code' => 0, 'msg' => '手机号格式不正确');
        }
        if (!$data['code']) {
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
    }

    public function check_register($data){
        $RegisterValidate = Loader::Validate('Register');
        if (!$RegisterValidate->check($data)) {
            $baocuo = $RegisterValidate->getError();
            return json(['code' => 0, 'msg' => $baocuo]);
        }
        if ($data['password'] != $data['re_password']) {
            return json(['code' => 0, 'msg' => '两次密码输入不一致']);
        }
            // 验证码
            // $checkData['sms_type'] = $data['sms_type'];
            // $checkData['code'] = $data['code'];
            // $checkData['phone'] = $data['mobile'];            
            // $res = checkPhoneCode($checkData);
            // if($res['code']==0){
            //     return json(['code'=>0,'msg'=> $res['msg']]);
            // }        
    }
}
