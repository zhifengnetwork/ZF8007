<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Login extends validate
{

    protected $rule = [
        'mobile1'         => 'require|checkMoblie',
        'password'        => 'require|chsDash|length:6,20',
        're_password'     => 'require|chsDash|length:6,20'        
    ];
    protected $message = [
        
        'mobile1.require'      => '请填写手机号',
        'password.require'    => '请填写密码' ,
        'password.chsDash'    => '密码只能是数字字母_或者-',
        'password.length'     => '密码长度是4到16位',
        're_password.require' => '请填写确认密码',
        're_password.chsDash'    => '确认密码只能是数字字母_或者-',
        're_password.length'     => '确认密码长度是4到16位',                 
    ];
    protected $scene = [
        'login_commit' => [ 'mobile1', 'password'],
        'find_commit' => [ 'mobile1', 'password', 're_password'],
        // 'del' => ['admin_id'],
    ];
    /**
     * 手机号码
     */
    protected function checkMoblie($value){
        if(preg_match( "/^1[3456789]\d{9}$/",$value)){
            return true;
        }else{
            return '手机号输入有误！';
        }
    }

}
