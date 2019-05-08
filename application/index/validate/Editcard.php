<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class Editcard extends validate
{

    protected $rule = [
        'value'         => 'require|chsAlpha',
        'user_name'     => 'require|chsAlpha',
        'account'       => 'number',
        'address'       => 'chsAlphaNum'
    ];
    protected $message = [
        
        'value.require'      => '请填写手机号',
        'value.chsAlpha'    => '银行名称必须是汉字或者字母',
        'user_name.require'      => '请填写姓名',
        'user_name.chsAlpha'      => '姓名必须是数字或者字母',
        'account.number'            => '卡号必须为数字',
        'address.chsAlphaNum'          => '地址是汉字字母或者数字'             
    ];
    // protected $scene = [
    //     'login_commit' => [ 'mobile1', 'password'],
    //     'find_commit' => [ 'mobile1', 'password', 're_password'],
    //     // 'del' => ['admin_id'],
    // ];


}