<?php
namespace app\admin\validate;

use \think\Validate;


class Money extends validate
{

    protected $rule = [
        'pack_name'          => 'require|chsAlphaNum|length:2,16|unique:package',
        // 'package'     => '|unique:pack_name',
        'pack_time'   => 'require|checkNum',
        'pack_money'  => 'require|checkFloat',
        'first_rebate_money' => 'require|checkFloat',
        'second_rebate_money' => 'require|checkFloat'

        // 'password'      => 'require|length:4,16',
        // 'password2'     => 'require|length:4,16',
        // 'group_id'      => 'require',
    ];
    protected $message = [
        'pack_name.unique'           => '已存在此套餐名',
        'pack_name.require'          => '套餐名必填',
        'pack_name.chsAlpha'         => '套餐名只能汉字字母数字',
        'pack_name.length'           => '套餐名长度2-16位',
        // 'pack_name.unique'           =>  '已存在此套餐名',
        'pack_time.require'          => '请填写套餐时长',
        // 'pack_time.checkNum'         => '请填写正整数',
        'pack_money.require'         => '请填写套餐时长',
        'first_rebate_money.require'       => '请填写一级返佣金额',
        'second_rebate_money.require'       => '请填写二级返佣金额',
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
