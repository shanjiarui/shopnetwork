<?php
//完善使用验证器功能 跟正则差不多的那个
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;
use think\Db;
use Request;
use Session;
class Login extends Controller
{
    public function login()
    {
    	return $this->fetch('login/login');
    }
    public function loginAction()
    {
    	$name=Request::post('name');
    	$password=Request::post('password');
    	$code=Request::post('code');
    	// echo $name;
    	// echo $password;
    	// echo $code;
	    if(!captcha_check($code)){
		 	$js=['code'=>'0','status'=>'error','data'=>'验证码错误！'];
		 	echo json_encode($js);
		 	die;
		}
		$arr=Db::query("select * from user where user_name='$name' and password='$password'");
		if (empty($arr)) {
			$js=['code'=>'0','status'=>'error','data'=>'账号或密码错误！'];
		 	echo json_encode($js);
		 	die;
		}
		$js=['code'=>'1','status'=>'ok','data'=>'登录成功！'];
		Session::set('id',$arr['0']['id']);
		Session::set('name',$name);
		echo json_encode($js);
    }
    
}
