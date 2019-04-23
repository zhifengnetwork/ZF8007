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
//        echo $type;
        if(in_array($type,array(1,2,3,4))){
            $page=10;
            $max=($num-1)*$page+1;
            $list=Db::name('interface_recorder')->where(['type'=>$type])->order('lottery_date desc')->limit($max,$page)->select();
//print_r($list);die;
            foreach ($list as $key=>$value){
                $list[$key]['lottery_number']=explode(',',$value['lottery_number']);
                $list[$key]['lottery_time']=date('Y',$value['lottery_time'])."年".date('m',$value['lottery_time'])."月".date('d',$value['lottery_time']).'日'.' '.date('H:i:s',$value['lottery_time']);
            }
//            var_dump($list);die;
//            $pages = $list->render();
            $this->assign('list', $list);
            $this->assign('page', $num);
            //展示最新一条
            switch ($type){
                case 1:
                    $type='幸运飞艇';
                    break;
                case 2:
                    $type='快乐飞艇';
                    break;
                case 3:
                    $type='快乐赛车';
                    break;
                case 4:
                    $type='快乐时时彩';
                    break;
            }
            $this->assign('type',$type);
            return $this->fetch();
        }else{
            echo '参数错误';
        }
    }
}
