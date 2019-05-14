<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/22 0022
 * Time: 17:23
 */

namespace app\admin\controller;
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set("PRC");
use think\Db;

class Hope
{
    /**
     * 每分钟向接口发送一次请求  将期号大于表中最大期号的数据存起来
     * 1、查询各个最大的期号
     * 2、获取接口信息
     * 3、循环判断是否大于目前最大的期号
     * 4、将超过的期号存起来
     * 5、插入日志记录
     */
    public function happy_is_important(){
        //幸运飞艇每天4点06-13点09分是闭市的   不在请求
        $luck_start=strtotime(date('Y-m-d 04:06:00',time()));
        $luck_end=strtotime(date('Y-m-d 13:09:00',time()));
        if(time()<$luck_start || time()>$luck_end){
            $luck_issue=$this->get_max_issue(1);
            $luck_url='http://api.b1api.com/api?p=json&t=xyft&limit=1&token=6E84023D0C29F550';
            $this->deposit_luck($luck_issue,$luck_url,1);
        }
        //快乐飞艇 快乐赛车 快乐时时彩  每天6点26-7:30闭市   不再发送请求
        $happy_start=strtotime(date('Y-m-d 06:26:00',time()));
        $happy_end=strtotime(date('Y-m-d 07:03:00',time()));
        if(time()<$happy_start || time()>$happy_end){
            //获取四个最大的期号   1幸运飞艇  2快乐飞艇  3快乐赛车  4快乐时时彩

            $airship_issue=$this->get_max_issue(2);
            $racing_issue=$this->get_max_issue(3);
            $lottery_issue=$this->get_max_issue(4);
            //4个接口连接

            $airship_url='https://api.happylottery.com/data/airship/last.xml';
            $racing_url='https://api.happylottery.com/data/racing/last.xml';
            $lottery_url='https://api.happylottery.com/data/lottery/last.xml';

            $this->deposit($airship_issue,$airship_url,2);
            $this->deposit($racing_issue,$racing_url,3);
            $this->deposit($lottery_issue,$lottery_url,4);
        }
    }

    public function deposit($max_issue,$url,$type){
        if($max_issue!=0){
            $lockfile='a.txt';
            if(file_exists($lockfile)){
                exit();
            }else{
                $file=fopen($lockfile,'a+');
                fwrite($file,'123');
                fclose($file);
            }
            $luck_data=$this->get_interface_infomation($url);
            if(isset($luck_data) && !empty($luck_data)){
                unlink($lockfile);
                if($max_issue==1){
                    if(isset($luck_data) && !empty($luck_data)){
                        foreach ($luck_data as $key=>$value){
                            $lottery_time=strtotime($value['time']);
                            $this->save_interface($type,$value['issue'],$value['numbers'],$lottery_time,time());
                            echo '存入一条类型为'.$type.'，期号为'.$value['issue'].'的数据/n';
                        }
                    }
                }else{
                    if(isset($luck_data) && !empty($luck_data)){
                        foreach ($luck_data as $key=>$value){
                            if($value['issue']>$max_issue){
                                //存起来
                                $lottery_time=strtotime($value['time']);
                                $this->save_interface($type,$value['issue'],$value['numbers'],$lottery_time,time());
                                echo '存入一条类型为'.$type.'，期号为'.$value['issue'].'的数据/n';
                            }
                        }
                    }
                }
            }
        }
    }
    //专为幸运飞艇设置
    public function deposit_luck($max_issue,$url,$type){
        if($max_issue!=0){
            $luck_data=$this->get_first_imformation($url);
            if($max_issue==1){
                if(isset($luck_data) && !empty($luck_data)){
                    foreach ($luck_data as $key=>$value){
                        $lottery_time=strtotime($value['opentime']);
                        $value['opencode']=explode(',',$value['opencode']);
                        $opencode='';
                        foreach ($value['opencode'] as $k=>$v){
                            $opencode.=ltrim($v,0).',';
                        }
                        $opencode=rtrim($opencode,',');
                        $this->save_interface($type,$value['expect'],$opencode,$lottery_time,time());
                        echo '存入一条类型为'.$type.'，期号为'.$value['expect'].'的数据/n';
                    }
                }
            }else{
                if(isset($luck_data) && !empty($luck_data)){
                    foreach ($luck_data as $key=>$value){
                        if($value['expect']>$max_issue){
                            //存起来
                            $lottery_time=strtotime($value['opentime']);
                            $value['opencode']=explode(',',$value['opencode']);
                            $opencode='';
                            foreach ($value['opencode'] as $k=>$v){
                                $opencode.=ltrim($v,0).',';
                            }
                            $opencode=rtrim($opencode,',');
                            $this->save_interface($type,$value['expect'],$opencode,$lottery_time,time());
                            echo '存入一条类型为'.$type.'，期号为'.$value['expect'].'的数据/n';
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $type  期号类型
     * 获取最大期号
     */
    public function get_max_issue($type){
        if(in_array($type,array(1,2,3,4))){
            $max_issue=Db::name('interface_recorder_test')->where(['type'=>$type])->order('lottery_date desc')->find();
            if(isset($max_issue)){
                return $max_issue['lottery_date'];
            }else{
                return 1;
            }
        }else{
            return 0;
        }
    }

    /**
     * 请求接口返回数据
     * @param $url  接口链接
     * @return mixed
     */
    public function get_interface_infomation($url){
        ignore_user_abort(true);
        set_time_limit(0);
//        $data = file_get_contents("php://input");
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
//curl_setopt($curl, CURLOPT_GET, 1); // 发送一个常规的Post请求
//curl_setopt($curl, CURLOPT_POSTFIELDS, array()); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $tmpInfo = curl_exec($curl);     //返回api的json对象
//关闭URL请求
        curl_close($curl);
        $tmpInfo=json_decode(json_encode(simplexml_load_string($tmpInfo)),true);
        return $tmpInfo['item'];
    }
    //幸运飞艇接口处理
    private function get_first_imformation($src){
        //防止GET本地缓存，增加随机数
        $src .= (strpos($src,'?')>0 ? '&':'?').'_='.time();
        $html = file_get_contents($src);
        $json = json_decode($html,true);
        if (isset($json['row'])){
            return $json['data'];
        }else{
            return 0;
        }
    }
    private function save_interface($type,$lottery_date,$lottery_number,$lottery_time,$add_time){
        $data['type']=$type;
        $data['lottery_date']=$lottery_date;
        $data['lottery_number']=$lottery_number;
        $data['lottery_time']=$lottery_time;
        $data['add_time']=$add_time;
        $data['type_lottery_date']=$type."_".$lottery_date;
        try{
            Db::name('interface_recorder_test')->insert($data);
        }catch(\Exception $e){
            echo "重复插入异常/n";
        }
    }
}