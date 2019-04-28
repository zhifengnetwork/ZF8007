<?php
namespace app\index\validate;

use \think\Validate;
//品牌验证器

class User extends validate
{

    protected $rule = [
        'nickname'         => 'require|chsAlpha',        
    ];
    protected $message = [
        
        'nickname.require'      => '请填写手机号',
        'nickname.chsAlpha'    => '密码只能是汉字或者字母',                
    ];
    // protected $scene = [
    //     'login_commit' => [ 'mobile1', 'password'],
    //     'find_commit' => [ 'mobile1', 'password', 're_password'],
    //     // 'del' => ['admin_id'],
    // ];


}