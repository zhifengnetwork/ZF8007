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
    public function lists(){
        $seach = isset($_GET['seach']) ? $_GET['seach'] : '';
        $m_conditions   = isset($seach['m_conditions']) ? $seach['m_conditions'] : '';
        $datemin        = isset($seach['datemin']) ? $seach['datemin'] : '';
        $datemax        = isset($seach['datemax']) ? $seach['datemax'] : '';
       
        $where = '';
        if($seach){
            // 搜索条件
            $where=$this->s_condition($seach['m_conditions'],$seach['datemin'], $seach['datemax']);
            $seach = [
                'm_conditions'  => $m_conditions,
                'datemin'       => strtotime($datemin),
                'datemax'       => strtotime($datemax),
            ];
            $this->assign('seach', $seach); 
        }
        // 列出数据
        $list = Db::name('package')->where($where)->order('add_time desc')->paginate(10, false, ['query' => request()->param()]);
        $num = count($list);
        
        $this->assign('num', $num); 
        // $this->assign('cname', $cname); 
    	$this->assign('list',$list);        
        return $this->fetch();
    }
    // 搜索条件
    public function s_condition($conditions, $datemins, $datemaxs)
    {
        $time = " 23:59:59";
        if ($conditions) {
            $m_conditions = str_replace(' ', '', $conditions);
            $where['pack_name'] = ['like', "%$m_conditions%"];
        }
        if ($datemins && $datemaxs) {
            $datemin = strtotime($datemins);
            $datemax = strtotime($datemaxs);
            $where['add_time'] = [['>= time', $datemin], ['<= time', $datemax], 'and'];
        } elseif ($datemins) {
            $where['add_time'] = ['>= time', strtotime($datemins)];
        } elseif ($datemaxs) {
            $where['add_time'] = ['<= time', strtotime($datemaxs)];
        }
        return $where;
    }

    /**
     * 添加套餐
     * 
     */
    public function add(){
         $data = input();
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

    /**
     * 分组删除和批量删除
     */
    public function del(){
        $data = input('post.');
        
        // dump($data);exit;
        if($_POST){
            if($data['act'] == 'batchdel'){
                    $id = json_decode($data['id'], true);
                    $where['id'] = array('in', $id);
                    $res = Db::name('package')->where($where)->delete();
            }else{
                    // $is_super = Db::name('admin')->where('id',$data['id'])->value('is_super');
                    // if ($is_super == 1) {
                    //     return json(['status' => -1, 'msg' => '超级管理员不能删除！']);
                    // }else{
                        $res = Db::name('package')->where('id', $data['id'])->delete();
                    
                    // } 
            }
            if($res){
                return json(['status'=>1,'msg'=>'操作成功']);
            }else{
                return json(['status'=>-1,'msg'=>'操作失败']);
            }
        }
 
        // Db::name('admin')->where('name', $data['name'])->select();
    }

    /**
     * 佣金明细
     */
    public function commission()
    {
        $where['id'] = ['>', 0];
        $datemin = '';
        $datemax = '';
        $seach = isset($_GET['seach']) ? $_GET['seach'] : '';
        $page = 10;
        //搜索条件
        if($seach){
            $page = 0;
            $time = " 23:59:59";
//            if($seach['m_conditions']){
//                $m_conditions = str_replace(' ', '', $seach['m_conditions']);
//                $where['first_leader'] = ['like',"%$m_conditions%"];
//            }
            if ($seach['datemin'] && $seach['datemax']) {
                $datemin = strtotime($seach['datemin']);
                $datemax = strtotime($seach['datemax'].$time);
                $where['rebate_time'] = [['>= time',$datemin],['<= time',$datemax],'and'];
            } elseif ($seach['datemin']) {
                $where['rebate_time'] = ['>= time',strtotime($seach['datemin'])];
            } elseif ($seach['datemax']) {
                $where['rebate_time'] = ['<= time',strtotime($seach['datemax'].$time)];
            }
        }

        $this->assign('seach',$seach);
        $list = Db::name('rebate_log')->where($where)->paginate($page);
        $count = count($list);
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }

}