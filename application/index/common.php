<?php

/**
 * 公共文件
 */

 use think\Db;
use think\Session;
use think\Request;
use think\Controller;
//获取验证码短信
function getPhoneCode($data)
{

    if (!$data['sms_type'] || !$data['phone']) {
        return array('code' => 0, 'msg' => '缺少验证参数');
    }
    // 判断手机号是否合法
    $check_phone = check_mobile_number($data['phone']);
    
    // 判断手机号是否存在数据库
    if( $data['sms_type'] == 1){
        if ($check_phone) {
            $is_phone_db = Db::name('users')->where(['mobile' => $data['phone']])->find();
            if ($is_phone_db) {
                return array('code' => 0, 'msg' => '已存在此手机号！');
            }
        } else {
            return array('code' => 0, 'msg' => '手机号格式不正确');
        }
    }

    $limit_time = 60; // 60秒以内不能重复获取
    $where['phone'] = $data['phone'];
    $where['sms_type'] = $data['sms_type'];
    $nowTime = time();
    $list = Db::query("select * from zf_verify_code where phone={$data['phone']} and sms_type={$data['sms_type']} and '{$nowTime}'-create_time<{$limit_time} limit 0,5");
    $cnt = count($list);
    // 1分钟
    if ($cnt > 1) {
        return array('code' => 0, 'msg' => '获取验证码过于频繁，请稍后再试');
    }
    $code = rand(123456, 999999);
    $tpl = '【SC】您的手机验证码：' . $code . ' 若非您本人操作，请忽略本短信。';
    // $content=str_replace('{$code}',$code,$tpl);
    $content = $tpl;
    $result = sendSms($data['phone'], $content);
    if ($result != '1') {
        // $res_num = strpos($result,'ok');
        // if($res_num != 8){
        return array('code' => 0, 'msg' => '短信发送失败-');
    }

    // 插入verify_code记录
    $db_data = array(
        'code' => $code,
        'phone' => $data['phone'],
        'sms_type' => $data['sms_type'],
        'create_time' => time(),
        // 'create_ip'=>CLIENT_IP,
        'sms_con' => $content
    );
    $res = Db::name('verify_code')->insert($db_data);
    if (!$res) {
        return array('code' => 0, 'msg' => '系统繁忙请稍后再试');
    }
    return array('code' => 200, 'msg' => '已发送成功');
}

/**
 * 手机号格式检查
 * @param string $mobile
 * @return bool
 */
function check_mobile_number($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    $reg = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#';

    return preg_match($reg, $mobile) ? true : false;
}

// 获取短信验证码接口
function sendSms($phone,$content){

    $smsCode = rand(123456,999999);
    $post_data = array();
    $post_data['userid'] = 2869;
    $post_data['account'] = 'qx3886';
    $post_data['password'] = '20190427';
    $post_data['content'] = $content; // 短信的内容，内容需要UTF-8编码
    $post_data['mobile'] = $phone; // 发信发送的目的号码.多个号码之间用半角逗号隔开 
    $post_data['sendtime'] = ''; // 为空表示立即发送，定时发送格式2010-10-24 09:08:10
    // $url='http://120.25.105.164:8888/sms.aspx?action=send';
    $o='';
    foreach ($post_data as $k=>$v)
    {
    $o.="$k=".urlencode($v).'&';
    }
    // dump($o);
    // exit;
    $post_data=substr($o,0,-1);
    $result= curl_post($url,$post_data);
    // return $result['output'];
    return $result;
}

// 发送验证码
function curl_post($url,$data='',$timeout=30){
    $arrCurlResult = array();
    $ch = curl_init();
    //curl_setopt ($ch, CURLOPT_SAFE_UPLOAD, false);

    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//ssl检测跳过
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HEADER, false);
    // curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    // curl_setopt ($ch,CURLOPT_REFERER,"");
    // $output = curl_exec($ch);
    // $responseCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    // $arrCurlResult['output'] = $output;//返回结果
    // $arrCurlResult['response_code'] = $responseCode;//返回http状态
    // curl_close($ch);
    // unset($ch);
    // return $arrCurlResult;

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);
    unset($ch);
    return $result;
}
// 校验手机验证码
function checkPhoneCode($data){
	if(!$data['sms_type']||!$data['code']||!$data['phone']){
        return array('code' => 0, 'msg' => '缺少验证参数');
    }
    $item = Db::name('verify_code')->where(['phone'=>$data['phone'], 'sms_type'=>$data['sms_type']])->order('id desc')->find();
	if(!$item['id']){
        return array('code' => 0, 'msg' => '该验证码不正确');
    }
	if($item['status']||$item['verify_num']>2){
        return array('code' => 0, 'msg' => '请重新获取验证码');
    }
    
	//查到验证码且验证使用未达到限制次数
	$msg='';
	$db_data=array('verify_num'=>$item['verify_num']+1);
	if($data['code']==$item['code']){
		//检测验证码有效期
		if(time()-$item['create_time']>1800){
			$msg='该验证码已失效';
			$db_data['status']=1;
		}else{
			$db_data['status']=2;
		}
	}else{
		$msg='该验证码不正确';
		if($db_data['verify_num']>2){
			$db_data['status']=1;
		}
	}
    $db_data['verify_time'] = time();
    $res = Db::name('verify_code')->where(['id'=>$item['id']])->update($db_data);
	if(!$res){
		$msg='该验证码不正确';
	}
	if($msg){
        return array('code' => 0, 'msg' => $msg);
	}
    return array('code' => 200, 'msg' => '验证通过');
}
/**
 *检查用户是否登陆
 */
function check_user(){
    $user = Session::get('user');
    if (empty($user)) {
         $url = "http://" . $_SERVER['HTTP_HOST'] . "/index/login/index";
//        $this->redirect('/index/login/index');
        header("refresh:1;url=$url");
        exit;
    }
}
//弹窗
function layer_error($msg)
{
    header("Content-type: text/html; charset=utf-8");
    echo '<script type="text/javascript" src="/public/static/mobile/js/jquery-1.8.3.js"></script>';
    echo '<script type="text/javascript" src="/public/static/mobile/js/layer/2.4/layer.js"></script>';
    echo "<script>$(function () {layer.msg('$msg',{icon:5,time:1000},function(){window.history.go(-1)})}); </script>";
/**
 * 调广告
 * $ad_id  传一个广告位id
 */
function get_ad($ad_id){
    if(is_numeric($ad_id) && $ad_id>0){
        $ads=Db::name('ad')->where(['pid'=>$ad_id])->select();
        if(isset($ads)&&!empty($ads)){
            return $ads;
        }
    }
    return array();
}