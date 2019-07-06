<?php
namespace app\admin\controller;
use think\Controller;
use Session;
class Common extends Controller
{
	//为什么只能用initialize？？？__construct用就报错
	public function initialize()
	{
		$name=Session::get('name');
		if (empty($name)) {
			$this->redirect('login/login');
		}else{
			$this->assign('name',$name);
		}
	}
	public function newToken()
	{
    	$new_token=uniqid();
		Session::set('token',$new_token);
		$js=['token'=>$new_token];
		echo json_encode($js);
	}
	public function token()
	{	
		$token = $this->request->token('__token__', 'sha1');
        $arr=['token'=>$token];
        echo json_encode($arr);
	}
}