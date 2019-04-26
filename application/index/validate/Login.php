<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Login extends validate
{

    protected $rule = [
        'mobile1'           => 'require|checkMoblie',
        'password'        => 'require|chsDash|length:4,16',
    ];
    protected $message = [
        
        'mobile.require'      => '请填写手机号',
        'password.require'    => '请填写密码' ,
        'password.chsDash'    => '密码只能是数字字母_或者-',
        'password.length'     => '密码长度是4到16位'          
    ];

    /**
     * 手机号码
     */
    protected function checkMoblie($value){
        if(preg_match( '/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/',$value)){
            return true;
        }else{
            return '手机号输入有误！';
        }
    }

}
