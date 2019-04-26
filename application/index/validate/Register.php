<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Register extends validate
{

    protected $rule = [
        'mobile'           => 'require|checkMoblie',
        'users'           => 'unique:mobile',
        'code'            => 'require',
        'password'        => 'require|chsDash|length:4,16',
    ];
    protected $message = [
        
        'mobile.require'      => '请填写手机号',
        // 'mobile.checkMobile'  => '手机号输入有误',
        'users.unique'        => '已存在此手机号',
        'code.require'        => '请填写验证码',
        'password.require'    => '请填写密码' ,
        'password.chsDash'    => '密码只能是数字字母_或者-',
        'password.length'     => '密码长度是4到16位'          
    ];
    // protected $scene = [
    //     'add' => ['name', 'jurisdiction'],
    //     'edit' => ['jurisdiction'],
    //     // 'del' => ['admin_id'],
    // ];
    /**
     * 非零正整数
     */
    protected function checkNum($value){
        if(preg_match( '/^[1-9]\d*$/',$value)){
            return true;
        }else{
            return '请输入正整数';
        }
    }
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
    /**
     * 非负浮点数
     */
    protected function checkFloat($value){
        if(preg_match( '/^\d+(\.\d+)?$/',$value)){
            return true;
        }else{
            return '请输入正数';
        }
    }

}
