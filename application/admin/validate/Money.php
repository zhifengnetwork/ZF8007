<?php
namespace app\admin\validate;

use \think\Validate;
//品牌验证器

class Money extends validate
{

    protected $rule = [
        'pack_name'          => 'require|chsAlpha|length:2,16|unique:package',
        // 'package'     => '|unique:pack_name',
        'pack_time'   => 'require',
        'pack_money'  => 'require',
        'rebate_money' => 'require'
        
        // 'password'      => 'require|length:4,16',
        // 'password2'     => 'require|length:4,16',
        // 'group_id'      => 'require',
    ];
    protected $message = [
        'pack_name.unique'           => '已存在此套餐名',
        'pack_name.require'          => '套餐名必填',
        'pack_name.chsAlpha'         => '套餐名只能汉字字母',
        'pack_name.length'           => '套餐名长度2-16位',
        // 'pack_name.unique'           =>  '已存在此套餐名',
        'pack_time.require'          => '请填写套餐时长',
        'pack_money.require'         => '请填写套餐时长',
        'rebate_money.require'       => '请填写返佣金额'
        // 'password.length' => '密码长度4-16',
        // 'password2.length' => '确认长度4-16',
        // 'name.alphaDash'        => '用户名只能英文和数字',
        // 'password.require'      => '密码必须填写',
        // 'password2.require'     => '确认密码必填',
        // 'group_id.require'      => '请选择角色',
    ];
    // protected $scene = [
    //     'add' => ['name', 'jurisdiction'],
    //     'edit' => ['jurisdiction'],
    //     // 'del' => ['admin_id'],
    // ];
}
