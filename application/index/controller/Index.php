<?php
namespace app\index\controller;

use think\Db;
class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        check_user();
    }
    //首页
    public function index()
    {
//        echo 123;
        //调用公告
        $article=Db::name('article')->where('type','0')->order('add_time desc')->find();
        if (isset($article['content'])&&$article['content']!=''){
            $article['content']=strip_tags($article['content']);
//            if(strlen($article['content'])>66){
//                $flag="...";
//            }else{
//                $flag='';
//            }
//            $article['content']=substr($article['content'],0,66).$flag;

        }else{
            $article['content']='暂无公告';
            $article['id']=0;
        }
        //调用广告
        $banner=get_ad(8);//首页banner
        $luck_lottery=get_ad(4);//幸运飞艇
        $happy_lottery=get_ad(5);//快乐飞艇
        $happy_car=get_ad(6);//快乐赛车
        $happy_color=get_ad(7);//快乐时时彩
        $this->assign('banner',$banner);
        $this->assign('luck_lottery',$luck_lottery);
        $this->assign('happy_lottery',$happy_lottery);
        $this->assign('happy_car',$happy_car);
        $this->assign('happy_color',$happy_color);

        //看看用户到期了没有
        $user_id=$this->user_id;
//        var_dump($user_id);
        $is_end=Db::name('users')->where(['id'=>$user_id])->where('end_time','<',time())->count();
//        var_dump($is_end);die;
        $this->assign('is_end',$is_end);
        $this->assign('notice',$article);
        return $this->fetch();
    }
    //列表页
    public function lottery_list(){
        $type=input('type');
        $type=(int)$type;
        $num=input('num',1);
        $is_ajax=input('is_ajax',0);
        $user_id=$this->user_id;
//        $user_id=1;//在登录做好之前暂用
//        echo $type;
        if(in_array($type,array(1,2,3,4))){
            $page=15;
//            if($num==0){
//                $max=1;
//            }else{
            $max=($num-1)*$page;
            if($max==90){
                $page=10;
            }elseif ($max>90){
                return false;
            }
//            }

//            var_dump((int)$type);
//            var_dump($max);
//            var_dump($page);die;
            $list=Db::name('interface_recorder')->where(['type'=>(int)$type])->order('lottery_date desc')->limit($max,$page)->select();
//            $list=Db::query();
//print_r($list);die;
            //把所有选择过的颜色存起来
            $all_color=array();
            foreach ($list as $key=>$value){
                $list[$key]['lottery_number_text']=$value['lottery_number'];
                $list[$key]['lottery_number']=explode(',',$value['lottery_number']);
//                foreach($list[$key]['lottery_number'] as $k=>$v){
//                    $list[$key]['lottery_color'][$k."_".$v]=$this->get_lottery_color($user_id,$type,$v);
//                }
                $list[$key]['lottery_time']=date('Y',$value['lottery_time'])."年".date('m',$value['lottery_time'])."月".date('d',$value['lottery_time']).'日'.' '.date('H:i:s',$value['lottery_time']);
            }
            if($type==4){
                for($i=1;$i<10;$i++){
                    $all_color['color'][$i]=$this->get_lottery_color($user_id,$type,$i,1);
                    $all_color['font_color'][$i]=$this->get_font_color($user_id,$type,$i,1);
                }
                $all_color['color'][10]=$this->get_lottery_color($user_id,$type,0,1);
                $all_color['font_color'][10]=$this->get_font_color($user_id,$type,0,1);
            }else{
                for($i=1;$i<11;$i++){
                    $all_color['color'][$i]=$this->get_lottery_color($user_id,$type,$i,1);
                    $all_color['font_color'][$i]=$this->get_font_color($user_id,$type,$i,1);
                }
            }
//            var_dump($list);die;
//            $pages = $list->render();
//            ksort($all_color);
//            var_dump($all_color);die;
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
                foreach ($list as $ke=>$val){
                    foreach($val['lottery_number'] as $l=>$p){
                        $list[$ke]['lottery_new_color'][$l.'-'.$p]=$this->get_lottery_color($user_id,$type,$p);
                        $list[$ke]['lottery_font_color'][$l.'-'.$p]=$this->get_font_color($user_id,$type,$p);
                    }
                }
                $this->ajaxReturn($list);
            }
            $this->assign('typed',$typed);
            $this->assign('type',$type);
            //倒计时
            if($type==2){
                $s=35;
            }elseif ($type==3){
                $s=20;
            }elseif ($type==4){
                $s=50;
            }else{
                $s=0;
            }
            $min=date('i')%5;
            $sec=date('s')-$s;
            if($min==0){
                if($sec>0){
                    $min=4;
                    $sec=60-$sec;
                }else{
                    $sec=0-$sec;
                }
            }else{
                if($sec>0){
                    $sec=60-$sec;
                    $min=4-$min;
                }else{
                    $min=5-$min;
                    $sec=0-$sec;
                }
            }
            if($sec<10){
                $sec='0'.$sec;
            }
            if($type==1){
                $close_start_time=strtotime(date('Y-m-d 04:04:00',time()));
                $close_end_time=strtotime(date("Y-m-d 13:09:00",time()));
                if(time()>=$close_start_time && time()<=$close_end_time){
                    $min=99;
                    $sec=99;
                }
            }else{
                $close_start_time=strtotime(date('Y-m-d 06:26:00',time()));
                $close_end_time=strtotime(date("Y-m-d 07:26:00",time()));
                if(time()>=$close_start_time && time()<=$close_end_time){
                    $min=99;
                    $sec=99;
                }
            }
            $this->assign('min',$min);
            $this->assign('sec',$sec);
            return $this->fetch();
        }else{
            echo '参数错误';
        }
    }

    //存颜色
    public function lottery_color(){
        $user_id=$this->user_id;
//        $user_id=1;//在前端登录做好之前用
        if(!isset($user_id) || $user_id<=0){
            $this->ajaxReturn(['status'=>-1,'msg'=>'请先登录']);
        }
        $type=input('type');
        $num=input('num');
        $group=input('group');
        $color=input('color');
        $font_color=input('font_color');
        $color=ltrim($color,'#');
        $font_color=ltrim($font_color,'#');

//        echo $type."~~~".$num."~~~".$group."~~~".$color;die;
        if(in_array($type,array(1,2,3,4)) && in_array($group,array(1,2,3,4,5,6)) && in_array($num,array(0,1,2,3,4,5,6,7,8,9,10)) && mb_strlen($color)==6){
            //先看看有没有记录
            $is_have=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'group'=>$group,'number'=>$num])->count();
            if($is_have){
                $res=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'group'=>$group,'number'=>$num])->update(['color'=>$color,'font_color'=>$font_color,'update_time'=>time()]);
            }else{
                $res=Db::name('lottery_color')->insert(['user_id'=>$user_id,'type'=>$type,'group'=>$group,'number'=>$num,'color'=>$color,'font_color'=>$font_color,'create_time'=>time(),'update_time'=>time()]);
            }
            $this->ajaxReturn(['status'=>$res,'mas'=>'变更成功']);
        }
    }
    //查颜色
    public function get_lottery_color($user_id,$type,$num,$is_date=0){
        if($is_date){
            $start_color=array('FFFFFF','FFFFFF','FFFFFF','FFFFFF','FFFFFF','FFFFFF');
        }else{
            $start_color=array('060835','060835','060835','060835','060835','060835');
        }
        if(in_array($type,array(1,2,3,4)) && in_array($num,array(0,1,2,3,4,5,6,7,8,9,10)) && $user_id>0){
            $lottery_color=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'number'=>$num])->select();
            if(isset($lottery_color)&&!empty($lottery_color)){
                foreach($lottery_color as $key=>$value){
                    $start_color[$value['group']-1]=$value['color'];
                }
            }
        }
        return $start_color;
    }
    //查数字本身颜色
    public function get_font_color($user_id,$type,$num,$is_date=0){
        if($is_date){
            $start_color=array('000000','000000','000000','000000','000000','000000');
        }else{
            $start_color=array('eff0f7','eff0f7','eff0f7','eff0f7','eff0f7','eff0f7');
        }
        if(in_array($type,array(1,2,3,4)) && in_array($num,array(0,1,2,3,4,5,6,7,8,9,10)) && $user_id>0){
            $lottery_color=Db::name('lottery_color')->where(['type'=>$type,'user_id'=>$user_id,'number'=>$num])->select();
            if(isset($lottery_color)&&!empty($lottery_color)){
                foreach($lottery_color as $key=>$value){
                    $start_color[$value['group']-1]=$value['font_color'];
                }
            }
        }
        return $start_color;
    }
    //获取最新一条记录
    public function new_lottery(){
        $type=input('type');

        $lottery_date=input('lottery_date');
        $user_id=$this->user_id;
//        $user_id=1;//用到登录做好了
        if($user_id>0 && in_array($type,array(1,2,3,4)) && $lottery_date>0){
            $new_lottery=Db::name('interface_recorder')->where(['type'=>$type])->where('lottery_date','>',$lottery_date)->find();
            //初始化六十个白色球
//            var_dump($new_lottery);die;
            if(isset($new_lottery) && !empty($new_lottery)){
                $data['lottery_time']=date('Y',$new_lottery['lottery_time'])."年".date('m',$new_lottery['lottery_time'])."月".date('d',$new_lottery['lottery_time']).'日'.' '.date('H:i:s',$new_lottery['lottery_time']);
                $data['lottery_number']=explode(',',$new_lottery['lottery_number']);
                $data['lottery_date']=$new_lottery['lottery_date'];
                $lottery_number=explode(',',$new_lottery['lottery_number']);
                foreach ($lottery_number as $k=>$v){
                    $data['lottery_color'][$k."-".$v]=array('060835','060835','060835','060835','060835','060835');
                    $data['font_color'][$k."-".$v]=array('ffffff','ffffff','ffffff','ffffff','ffffff','ffffff');
                }
//                var_dump($data['lottery_color']);die;
                //看看有没有配过色
                $lottery_color=Db::name('lottery_color')->where(['user_id'=>$user_id,'type'=>$type])->order('group asc')->select();
                if(isset($lottery_color) && !empty($lottery_color)){
//                    var_dump($lottery_color);die;
                    foreach ($lottery_color as $key=>$value){
                        foreach ($lottery_number as $ke=>$va){
                            $data['lottery_color'][$ke.'-'.$value['number']][$value['group']-1]=$value['color'];
                            $data['font_color'][$ke.'-'.$value['number']][$value['group']-1]=$value['font_color'];
                        }
                    }
                }
//                var_dump($data);
                //倒计时
                if($type==2){
                    $s=30;
                }elseif ($type==3){
                    $s=15;
                }elseif ($type==4){
                    $s=45;
                }else{
                    $s=5;
                }
                $min=date('i')%5;
                $sec=date('s')-$s;

                if($min==0 && $sec>0){
                    $min=4;
                    $sec=60-$sec;
                }elseif ($sec<0){
                    $min=5-$min;
                    $sec=0-$sec;
                }else{
                    $min=4-$min;
                    $sec=60-$sec;
                }
                if($type==1){
                    if($min==0){
                        $min=5+$min;
                    }
                }
                if($sec<10){
                    $sec='0'.$sec;
                }
                $data['min']=$min;
                $data['sec']=$sec;

                $this->ajaxReturn($data);
            }
        }
    }
}

































