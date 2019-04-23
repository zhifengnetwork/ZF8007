<?php
namespace app\index\controller;


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
//        echo $type;
        if(in_array($type,array(1,2,3,4))){
            
        }
    }
}
