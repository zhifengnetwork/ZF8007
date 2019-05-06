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
use think\Loader;

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
        // 登录时间
        $time = Session::get('time');
        $this->assign('time',$time);
        $this->assign('info',$info);
        return $this->fetch();
    }
    /**
     * 用户信息
     *  */ 
    public function data_info(){
        $info = Db::name('users')->where('id', $this->user_id)->find();
        $sex = [
          0 => '保密',
          1 => '男',
          2 => '女',
        ];
        $this->assign('sex',$sex);
        $this->assign('info', $info);
        return $this->fetch();
    }  

    /**
     * 更换密码
     */
    public function change_pwd(){
        $info = Db::name('users')->where('id', $this->user_id)->find();
        $this->assign('info', $info);       
        return $this->fetch();
    }

    /**
     * 更换头像
     */
    public function alter_avatar(){
        $info = Db::name('users')->where('id', $this->user_id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    /**
     * 更换昵称
     */
    public function alter_name()
    {
        $data = input('post.');
        if($_POST){
            //    验证
            $UserValidate = Loader::Validate('User');
            if (!$UserValidate->check($data)) {
                $baocuo = $UserValidate->getError();
                return json(['code' => 0, 'msg' => $baocuo]);
            }
            $data1 = [
                'id'       => $this->user_id,
                'nickname' => trim($data['nickname'])
            ];
            $res = Db::name('users')->update($data1);
            if($res){
                return json(['code'=>1,'msg'=>'更新成功']);
            }else{
                return json(['code'=>0,'msg'=>'更新失败']);
            }         
        }
        return $this->fetch();
    }
    /**
     * 更换性别
     */
    public function alter_sex()
    {
        $data = input('post.');
        if($_POST){
            $data1 = [
                'id'  => $this->user_id,
                'sex' => intval($data['sex'])
            ];
            $res = Db::name('users')->update($data1);
            if ($res) {
                return json(['code' => 1, 'msg' => '更新成功']);
            } else {
                return json(['code' => 0, 'msg' => '更新失败']);
            }                            
        }

        $info = Db::name('users')->where('id', $this->user_id)->find();
        $sex = [
            0 => '保密',
            1 => '男',
            2 => '女',
        ];
        $this->assign('sex', $sex);
        $this->assign('info', $info);        
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
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/index/login/register?id=" . $info['id'];
        $this->assign('info',$info);
        $this->assign('url',$url);
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
            $data = input('post.');//dump($data);die;
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
                'pay_code' => '/'.$img_path . $img_name,
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
                // // 在线时长
                $o_time  = Db::name('users')->where('id', $this->user_id)->value('online_time');
                $t = Session::get('time');
                $t_out = time();
                $online_time = $t_out-$t+$o_time;
                $e_time  = Db::name('users')->where('id',$this->user_id)->update(['online_time'=>$online_time]);
                if(!$e_time){
                    return json(['status' => 0, 'msg' => '退出失败，请稍后再试！']);
                }
                // if($e_time){
                //     $re_t    = $e_time - $re_time;
                //     $data = [
                //         'end_time' => $re_t
                //     ];
                //     Db::name('users')->where('id', $this->user_id)->update($data);
                Session::delete('user');
                // }
                
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
     * 上传头像
     */
    public function upload_avatar()
    {
        $base64 = input('post.dataImg');
        $user_id = $this->user_id;
        $res = $this->uploadImg($base64, $user_id);
        return $res;
    }

    /**
     * 处理base64
    */
    function uploadImg($base64,$user_id){
        header("content-type:text/html;charset=utf-8");
        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                $image_name = uniqid().'.jpg';
                //纯粹是看jpeg不爽才替换的
            }else{
                $image_name = uniqid().'.'.$result[2];
            }
            $image_file = "./uploads/".date('Ymd',time()).'/';
            if (!file_exists($image_file)) {

                mkdir($image_file,0755,true);
            }
            $image_url = "./uploads/".date('Ymd',time()).'/'."{$image_name}";
            $res_url = "/uploads/".date('Ymd',time()).'/'."{$image_name}";
            $res = Db::name('users')->where('id',$user_id)->update(['avatar' => $res_url]);
            $user = Db::name('users')->where('id', $user_id)->find();
            Session::set('user', $user);
            //服务器文件存储路径
            if ($res && file_put_contents($image_url, base64_decode(str_replace($result[1], '', $base64_image)))){
                return json(['code'=>200, 'msg'=>'上传成功', 'imgUrl'=>$res_url]);
            }else{
                return json(['code'=>0, 'msg'=>'上传失败']);

            }
        }else{
            return json(['code'=>0, 'msg'=>'上传失败..']);
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

									
    /*
     * 提现
     */
    public function withdraw()
    {
        $user_id = $this->user_id;
        if ($_GET){
            $select = input('');
            $select = (int)$select['select'];
//        dump($select);die;
            $way = Db::name('withdraw_way')->where('id',$select)->value('value');
            $this->assign('way',$way);
        }

        if ($_POST){
            $way = input('post.way');
            if (empty($way)){
                return json(['status'=>-1,'msg'=>'请选择支付方式']);
            }
            $money = input('post.money');
            $res = Db::name('users')->where('id',$user_id)->setDec('commission',$money);
            Db::name('withdraw_log')->insert([
                'user_id' => $user_id,
                'money' => $money,
                'withdraw_way' => $way,
                'status' => 0,
                'apply_time' => time()
            ]);
            if ($res){
                return json(['status'=>1,'msg'=>'提现成功']);
            }else{
                return json(['status'=>-1,'msg'=>'提现失败']);
            }
        }

        $commission = Db::name('users')->where('id',$user_id)->value('commission');

        $this->assign('commission',$commission);

        return $this->fetch();
    }

    /*
     * 提现方式展示
     */
    public function withdraw_way()
    {
        $alipay = Db::name('withdraw_way')->where(['user_id'=>$this->user_id,'withdraw_way'=>0])->find();
        $bank = Db::name('withdraw_way')->where(['user_id'=>$this->user_id,'withdraw_way'=>1])->find();

        $this->assign('alipay',$alipay);
        $this->assign('bank',$bank);
        return $this->fetch();
    }

    /*
     * 提现支付宝编辑
     */
    public function pay()
    {
        if ($_POST){
            $data = input('post.');//dump($data);die;
            $data1 = [
                'user_id'=>$data['id'],
                'withdraw_way'=>0,
                'value'=>'支付宝',
                'account'=>$data['account'],
                'user_name'=>$data['user_name']
            ];
            $field = ['user_id'=>$data['id'],'withdraw_way'=>0];
            //判断是否已经拥有数据
            $is_has = Db::name('withdraw_way')->where($field)->find();
            if ($is_has){
                $res = Db::name('withdraw_way')->where($field)->update($data1);
            }else{
                $res = Db::name('withdraw_way')->insert($data1);
            }
//            $res = Db::name('withdraw_way')
//                ->where(['user_id'=>$data['id'],'withdraw_way'=>0])
//                ->update(['account'=>$data['account'],'user_name'=>$data['user_name']]);
            if ($res) {
                return json(['status' => 1, 'msg' => '编辑成功']);
            }else{
                return json(['status' => -1, 'msg' => '编辑失败']);
            }
        }
        $user_id = $this->user_id;
        $info = Db::name('withdraw_way')->where(['user_id'=>$this->user_id,'withdraw_way'=>0])->find();

        $this->assign('user_id',$user_id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    /*
     * 提现银行编辑
     */
    public function editcard()
    {
        if ($_POST){
            $data = input('post.');//dump($data);die;
            $data1 = [
                'user_id'=>$data['id'],
                'withdraw_way'=>1,
                'value'=>$data['value'],
                'account'=>$data['account'],
                'user_name'=>$data['user_name'],
                'address'=>$data['address']
            ];
            $field = ['user_id'=>$data['id'],'withdraw_way'=>1];
            //判断是否已经拥有数据
            $is_has = Db::name('withdraw_way')->where($field)->find();
            if ($is_has){
                $res = Db::name('withdraw_way')->where($field)->update($data1);
            }else{
                $res = Db::name('withdraw_way')->insert($data1);
            }

//            $res = Db::name('withdraw_way')
//                ->where(['user_id'=>$data['id'],'withdraw_way'=>1])
//                ->update([
//                'value'=>$data['value'],
//                'account'=>$data['account'],
//                'user_name'=>$data['user_name'],
//                'address'=>$data['address']
//            ]);
            if ($res) {
                return json(['status' => 1, 'msg' => '编辑成功']);
            }else{
                return json(['status' => -1, 'msg' => '编辑失败']);
            }
        }
        $user_id = $this->user_id;
        $info = Db::name('withdraw_way')->where(['user_id'=>$this->user_id,'withdraw_way'=>1])->find();
//        dump($info);die;
        $this->assign('user_id',$user_id);
        $this->assign('info',$info);
        return $this->fetch();
    }


    /**
     * 获取退出时间并更新套餐剩余时间
     */
    public function get_time(){
        $e = input('post.end_time');
        if($_POST){
            $end_time   = time();
            $start_time = Session::get('time');

            $data1 = [
                'end_time' => $e
            ];            

                $res = Db::name('users')->where('id', $this->user_id)->update($data1);
                if($res){
                    return json(['code'=>1]);
                }else{
                    return json(['code'=>0]);
                }
        }
    }
}