<?php
namespace app\index\controller;

use think\Db;
class Index extends Base
{
    //首页
    public function index()
    {
//        echo 123;
        return $this->fetch();
    }
    //列表页
    public function lottery_list(){
        $type=input('type');
        $num=input('num',1);
        $is_ajax=input('is_ajax',0);
        $user_id=session('user.user_id');
        $user_id=1;//在登录做好之前暂用
//        echo $type;
        if(in_array($type,array(1,2,3,4))){
            $page=10;
            $max=($num-1)*$page+1;
            $list=Db::name('interface_recorder')->where(['type'=>$type])->order('lottery_date desc')->limit($max,$page)->select();
//print_r($list);die;
            //把所有选择过的颜色存起来
            $all_color=array();
            foreach ($list as $key=>$value){
                $list[$key]['lottery_number']=explode(',',$value['lottery_number']);
                foreach($list[$key]['lottery_number'] as $k=>$v){
                    $list[$key]['lottery_color'][$v]=$this->get_lottery_color($user_id,$type,$v);
                    $all_color[$v]=$list[$key]['lottery_color'][$v];
                }
                $list[$key]['lottery_time']=date('Y',$value['lottery_time'])."年".date('m',$value['lottery_time'])."月".date('d',$value['lottery_time']).'日'.' '.date('H:i:s',$value['lottery_time']);
            }
//            var_dump($list);die;
//            $pages = $list->render();
            ksort($all_color);
            $this->assign('list', $list);
            $this->assign('page', $num);
            $this->assign('all_color',$all_color);
//            var_dump($all_color);die;
            //展示最新一条
            switch ($type){
                case 1:
                    $typed='幸运飞艇';
                    break;
                case 2:
                    $typed='快乐飞艇';
                    break;
                case 3:
                    $typed='快乐赛车';
                    break;
                case 4:
                    $typed='快乐时时彩';
                    break;
            }
            if($is_ajax){
                $this->ajaxReturn($list);
            }
            $this->assign('typed',$typed);
            $this->assign('type',$type);
            return $this->fetch();
        }else{
            echo '参数错误';
        }
    }
    //存颜色
    public function lottery_color(){
        $user_id=session('user.user_id');
        $user_id=1;//在前端登录做好之前用
        if(!isset($user_id) || $user_id<=0){
            $this->ajaxReturn(['status'=>-1,'msg'=>'请先登录']);
        }
        $type=input('type');
        $num=input('num');
        $group=input('group');
        $color=input('color');

//        echo $type."~~~".$num."~~~".$group."~~~".$color;die;
        if(in_array($type,array(1,2,3,4)) && in_array($group,array(1,2,3,4,5,6)) && in_array($num,array(0,1,2,3,4,5,6,7,8,9,10)) && mb_strlen($color)==6){
            //先看看有没有记录
            $is_have=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'group'=>$group,'number'=>$num])->count();
            if($is_have){
                $res=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'group'=>$group,'number'=>$num])->update(['color'=>$color,'update_time'=>time()]);
            }else{
                $res=Db::name('lottery_color')->insert(['user_id'=>$user_id,'type'=>$type,'group'=>$group,'number'=>$num,'color'=>$color,'create_time'=>time(),'update_time'=>time()]);
            }
            $this->ajaxReturn(['status'=>$res,'mas'=>'变更成功']);
        }
    }
    //查颜色
    public function get_lottery_color($user_id=1,$type,$num){
        if(in_array($type,array(1,2,3,4)) && in_array($num,array(0,1,2,3,4,5,6,7,8,9,10)) && $user_id>0){
            $lottery_color=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'number'=>$num])->column('color');
            if(isset($lottery_color)&&!empty($lottery_color)){
                return $lottery_color;
            }
        }
        return 0;
    }
    //获取最新一条记录
    public function new_lottery(){
        $type=input('type');
        $lottery_number=input('lottery_number');
        $user_id=session('user.user_id');
        $user_id=1;//用到登录做好了
        if($user_id>0 && in_array($type,array(1,2,3,4)) && $lottery_number>0){
            Db::name('');
        }
    }
}

































