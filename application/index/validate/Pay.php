<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Pay extends validate
{

    protected $rule = [
        'account'       =>  'require|alphaNum|max:25',
        'user_name'     => 'require|chsAlpha|max:30',
    ];
    protected $message = [
        
        'user_name.require'       => '请填写姓名',
        'user_name.max'           => '姓名最大长度30位',
        'user_name.chsAlpha'      => '姓名必须是汉字或者字母',
        'account.alphaNum'        => '请填写数字或者字母',
        'account.require'         => '请填写账号',
        'account.max'             => '账号长度最大25位' 
        
    ];



}