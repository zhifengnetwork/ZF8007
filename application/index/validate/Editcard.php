<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Editcard extends validate
{

    protected $rule = [
        'value'         => 'require|chsAlpha',
        'user_name'     => 'require|chsAlpha',
        // 'account'       => 'number',
        'address'       => 'chsAlphaNum',
        'account'       =>  'require|alphaNum', 
    ];
    protected $message = [
        
        'value.require'           => '请填写银行名称',
        'value.chsAlpha'          => '银行名称必须是汉字或者字母',
        'user_name.require'       => '请填写姓名',
        'user_name.chsAlpha'      => '姓名必须是数字或者字母',
        'account.alphaNum'        => '请填写数字或者字母',
        'address.chsAlphaNum'          => '地址是汉字字母或者数字',
        'account.require'          => '请填写账号',
        
    ];
    protected $scene = [
        'editcard' => [ 'value', 'user_name','account','address'],
        'pay' => [ 'account','user_name', ],
        // 'del' => ['admin_id'],
    ];


}