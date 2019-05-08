<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Pay extends validate
{

    protected $rule = [
        'account'       =>  'require|alphaNum',
        'user_name'     => 'require|chsAlpha',
    ];
    protected $message = [
        
        'user_name.require'       => '请填写姓名',
        'user_name.chsAlpha'      => '姓名必须是数字或者字母',
        'account.alphaNum'        => '请填写数字或者字母',
        'account.require'          => '请填写账号',
        
    ];



}