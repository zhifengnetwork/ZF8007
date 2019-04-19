<?php

namespace app\admin\controller;
use think\Db;

/**
 * 根据采蜜订单自动处理发放蜜糖
 *
 */
class AutoQueen
{
	/**
	* 72小时不喂养蜂王   或者  购买之后60天蜂王自动死亡
	* 1、获取从现在开始72小时之前的时间戳
	* 2、获取购买蜂王72小时以上的所有蜂王信息
	* 3、循环判断从72小时至今有没有喂养记录
	* 4、72小时没有喂养记录 改变蜂王为死亡状态
	* 5、插入日志记录
	*
	*/
    public function verification_queen_status(){

//        $start_time=strtotime(date("Y-m-d 23:59:59",time()))-86400*3+1;
        $flag_time=strtotime("-3 day");//判断已经购买超过3天的
//        $now_time=strtotime(date("Y-m-d 23:59:59"),time());
        $need_die=M('user_bee')->where(['die_status'=>0])->where('adopt_time','<',$flag_time)->select();
//        var_dump($need_die);
        if(isset($need_die) && !empty($need_die)){
            foreach ($need_die as $key=>$value){
                //看有没有记录
                $res=M('bee_flow')->where(['bid'=>$value['id'],'type'=>804])->where('create_time','>',$flag_time)->count();
                if(!$res){
                    $result=M('user_bee')->where(['id'=>$value['id']])->save(['die_status'=>1]);
                    if($result){
                        echo 'ID为'.$value['id'].'由于72小时没喂养死掉了';
                    }else{
                        echo 'ID为'.$value['id'].'改变死亡状态失败';
                    }
                }else{
                    echo '暂时没有3天没有喂养的蜂王';
                }
            }
        }else{
            echo '没有发现3天以上的蜂王';
        }
        echo "<hr />";
        //60天之前
        $ago_time = strtotime("-60 day");
        $need_die=M('user_bee')->where(['die_status'=>0])->where('adopt_time','<=',$ago_time)->select();
//        var_dump($need_die);
        if(isset($need_die) && !empty($need_die)){
            foreach ($need_die as $key=>$value){
                $result=M('user_bee')->where(['id'=>$value['bid']])->save(['die_status'=>1]);
                if($result){
                    echo 'ID为'.$value['bid'].'由于已经工作了60天死掉了';
                }else{
                    echo 'ID为'.$value['bid'].'改变死亡状态失败';
                }
            }
        }else{
            echo '没有发现60天以上的蜂王';
        }
    }
}


