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
}