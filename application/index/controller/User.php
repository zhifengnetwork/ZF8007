<?php
/**
 * Created by PhpStorm.
 * User: MyPc
 * Date: 2019/4/26 0026
 * Time: 15:23
 */
namespace app\index\controller;

header('content-type:text/html;charset=utf-8');

use think\Db;
use think\Session;
use think\Request;

class User extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->check_user();
    }

    //个人中心首页
    public function mycenter()
    {
        
        $info = Db::name('users')->where('id', $this->user_id)->find();
        $time = Session::get('time');
        $this->assign('time',$time);
        $this->assign('info',$info);
        return $this->fetch();
    }

    //历史记录
    public function brokerage()
    {
        $user_id = $this->user_id;//dump($user_id);die;
        $info = Db::name('users')->where('id',$user_id)->find();
        $team_ids = Db::name('users')->where('first_leader',$user_id)->field('id ,nickname, commission')->select();
        $count = count($team_ids);
        $this->assign('info',$info);
        $this->assign('team_ids',$team_ids);
        $this->assign('count',$count);
        return $this->fetch();
    }
    //公告
    public function notice()
    {
        $data = Db::name('article')->where('type','0')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //免责说明
    public function disclaimer()
    {
        $data = Db::name('article')->where('type','1')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //使用说明
    public function use_notice()
    {
        $data = Db::name('article')->where('type','2')->order('add_time desc')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    //邀请链接
    public function invitelink()
    {
        $info = Db::name('users')->where('id',$this->user_id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    //上传凭证
    public function uploadfile(Request $request)
    {
        $pay_way = Db::name('config')->where('type','pay_setting')->select();
        $pay_way[0]['name'] = '支付宝';
        $pay_way[1]['name'] = '微信';
        $pay_way[0]['img'] = $pay_way[2]['value'];
        $pay_way[1]['img'] = $pay_way[3]['value'];

        $package = Db::name('package')->select();
        $this->assign('pay',$pay_way);
        $this->assign('package',$package);//dump($package);die;
        return $this->fetch();
    }

    public function upload(Request $request)
    {
        if ($_POST) {
            $data = input('post.');
            if (empty($data['files'])) {
                return json(['status' => -1, 'msg' => '请选择凭证上传']);
            }
            $img_path = 'public/upload/proof/';
            $img_name = md5(mt_rand(0, 100000) . time()) . '.png'; //文件名
//            $exten = substr($name,strrpos($name,'.')); //上传文件后缀名
            if (!is_dir(ROOT_PATH . $img_path)) {
                mkdir(ROOT_PATH . $img_path, 0777, true);
            }

            $base64_string = explode(',', $data['files']); //截取data:image/png;base64, 这个逗号后的字符
            $data1 = base64_decode($base64_string[1]);   //对截取后的字符使用base64_decode进行解码
            file_put_contents(ROOT_PATH . $img_path . $img_name, $data1); //写入文件并保存

            //写入数据库
            $packs = Db::name('package')->where('id', $data['pack'])->find();
            $pay_way = explode(':', $data['pays']);

            $res = Db::name('user_pay_log')->insert([
                'user_id' => $this->user_id,
                'package_id' => $packs['id'],
                'pay_money' => $packs['pack_money'],
                'pay_status' => 0,
                'pay_time' => time(),
                'pay_code' => ROOT_PATH . $img_path . $img_name,
                'pay_way' => $pay_way['0']
            ]);
            if ($res) {
                return json(['status' => 1, 'msg' => '上传成功']);
            } else {
                return json(['status' => -1, 'msg' => '上传失败']);
            }
        } else {
            return json(['status' => -1, 'msg' => '上传失败']);
        }
    }

     /**
      * 用户设置 
      */ 
    public function setting(){
        return $this->fetch();
    }
    /**
     * 退出
     */
    public function logout(){
        if($_POST){
           $check = Session::get('user');
           if($check){ 
                Session::delete('user');
                $user = Session::get('user');
                if (empty($user)) {
                    return json(['status' => 1, 'msg' => '退出成功！','url'=>'/index/login/index']);
                } else {
                    return json(['status' => 0, 'msg' => '退出失败，请稍后再试！']);
                }                
           }else{
               return json(['status' => 0, 'msg' => '退出异常，请稍后再试！']);
           }
        }
    }

    /**
     *检查用户是否登陆
     */      
    public function check_user(){
        $user = Session::get('user');
        if (empty($user)) {
            // $url = "http://" . $_SERVER['HTTP_HOST'] . "/index/login/index";
            $this->redirect('/index/login/index');
        }
    }
}