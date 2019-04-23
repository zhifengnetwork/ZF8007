<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/17 0017
 * Time: 21:14
 */
namespace app\admin\controller;

use think\Db;
use think\Session;
use think\Loader;
class Money extends Base
{
    public function index(){
        return $this->fetch();
    }
    /**
     * 套餐列表 
     */
    public function list(){
        return $this->fetch();
    }
    /**
     * 添加套餐
     * 
     */
    public function add(){
         $data = input();
         dump($data);exit;
         if($_POST){
            $moneyValidate = Loader::Validate('Money');
            if(!$moneyValidate->check($data)){
                $baocuo=$moneyValidate->getError();
                return json(['status'=>0,'msg'=> $baocuo]);
            }
            if($data['id']){   
                $data1 = [
                    'pack_name'  => $data['pack_name'],
                    'pack_money' => $data['pack_money'],
                    'pack_time'  => $data[ 'pack_time'],
                    'rebate_money'=> $data['rebate_money'],
                    'update_time' => time()
                ];                
                $res = Db::name('package')->where('id',$data['id'])->update($data1);
                if ($res) {
                    return json(['status' => 1, 'msg' => '更新成功！']);
                } else {
                    return json(['status' => 0, 'msg' => '更新失败！']);
                } 
            }
            $data1 = [
                'pack_name'  => $data['pack_name'],
                'pack_money' => $data['pack_money'],
                'pack_time'  => $data['pack_time'],
                'rebate_money' => $data['rebate_money'],
                'add_time' => time()
            ]; 
            $res = Db::name('package')->insert($data1);
            if($res){
                    return json(['status'=>1,'msg'=> '添加成功']);
            }else{
                    return json(['status'=>0,'msg'=> '添加失败']);
            }            
         }
        
        if(isset($data['id'])){
            $info = Db::name('package')->where('id',$data['id'])->find();
            $this->assign('info',$info); 
        }         
         
         return $this->fetch();
    }
}