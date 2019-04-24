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

class Member extends Base
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    //会员列表
    public function lists()
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
            if($seach['m_conditions']){
                $m_conditions = str_replace(' ', '', $seach['m_conditions']);
                $where['nickname|mobile|email'] = ['like',"%$m_conditions%"];
            }
            if ($seach['datemin'] && $seach['datemax']) {
                $datemin = strtotime($seach['datemin']);
                $datemax = strtotime($seach['datemax'].$time);
                $where['register_time'] = [['>= time',$datemin],['<= time',$datemax],'and'];
            } elseif ($seach['datemin']) {
                $where['register_time'] = ['>= time',strtotime($seach['datemin'])];
            } elseif ($seach['datemax']) {
                $where['register_time'] = ['<= time',strtotime($seach['datemax'].$time)];
            }
        }

        $list = Db::name('users')->where($where)->paginate($page);//dump($list);die;
        $count = count($list);
        $this->assign('seach',$seach);

        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //添加会员
    public function add()
    {
        $data = input('post.');
        $act = 'add';
        $this->assign('act',$act);
        $this->assign('data',$data);
        return $this->fetch();
    }

    //会员编辑
    public function edit()
    {
        $user_id = input('get.id/d');
        $info = Db::name('users')->where('id',$user_id)->find();
        $act = 'edit';
        $this->assign('info',$info);
        $this->assign('act',$act);
        $this->assign('user_id',$user_id);
        return $this->fetch();
    }

    public function handle()
    {
        $data = input('post.');//dump($data);die;
        $return = ['status'=>0,'msg'=>'参数错误']; //初始化返回信息
        if($data['act'] == 'add'){
            $is_name = Db::name('users')->where('nickname',$data['username'])->find();
            if(!empty($is_name)){
                return json(['status'=>0,'msg'=>'已经有此会员了！']);
            }
            $data1 = [
                'nickname'    => $data['username'],
                'mobile'=> $data['mobile'],
                'sex'   => $data['sex'],
                'email' => $data['email'],
                'register_time' => time()
            ];
            $res = Db::name('users')->insert($data1);
            if($res) {
                return json(['status'=> 1, 'msg'=> '添加成功']);
            }else {
                return json(['status'=> 0, 'msg'=> '添加失败，数据库未响应']);
            }
        }
        if($data['act'] == 'edit'){
            $data1 = [
                'nickname'    => $data['username'],
                'mobile'=> $data['mobile'],
                'sex'   => $data['sex'],
                'email' => $data['email'],
                'register_time' => time()
            ];
            $res = Db::name('users')->where('id',$data['user_id'])->update($data1);
            if($res) {
                return json(['status'=> 1, 'msg'=> '添加成功']);
            }else {
                return json(['status'=> 0, 'msg'=> '添加失败，数据库未响应']);
            }
        }

        if ($data['act'] == 'status') {
            $status = intval($data['status']);
            $status = ($status == 1) ? $status : 0;
            $bool = Db::name('users')->where('id',$data['id'])->update(['status'=>$status]);

            if ($bool) {
                return json(['code' => 1]);
            } else {
                return json(['code' => 0]);
            }
        }

    }

    //删除会员
    public function del()
    {
        $data = input('post.');
        if ($_POST){
            $id = json_decode($data['id'], true);
            $where['id'] = array('in', $id);
            $res = Db::table('zf_users')->where($where)->delete();
            if($res){
                return json(['status'=>1,'msg'=>'删除成功']);
            }else {
                return json(['status'=>-1,'msg'=>'删除失败']);
            }
        }
    }
    /**
     * 用户支付详情
     */
    public function pay_check(){
        // $list = Db::name('user_pay_log')
        // ->alias('p')
        // ->jion('users u',)
        // ->select();
        $list = Db::name('user_pay_log')
                ->alias('p')
                ->join('users u', 'u.id = p.user_id')
                ->join('package pa', 'pa.id = p.package_id')
                ->field( 'p.*,u.nickname,pa.pack_name')
                ->select();
        
        dump($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function level()
    {
        return $this->fetch();
    }

    public function scoreoperation()
    {
        return $this->fetch();
    }

    public function record_browse()
    {
        return $this->fetch();
    }

    public function record_download()
    {
        return $this->fetch();
    }

    public function record_share()
    {
        return $this->fetch();
    }

}