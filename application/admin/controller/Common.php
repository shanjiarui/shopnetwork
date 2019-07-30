<?php
namespace app\admin\controller;
use think\Controller;
use Session;
use gmars\rbac\Rbac;
use Request;
use Redis;
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
		$module=Request::module();
		$controller=Request::controller();
		$action=Request::action();
		$arr_controller=['Permission','Permissioncate','Role','User','Brand','Product'];
		$arr_action=['list','up_permission_action','add_action','del_permission','up_permission_category','del_permission_category','role_add_action','del_role','up_role','up_action','del_one','add_action','tree','my_update','del_one','add_action','select_all','up_img'];
		$path="$module/$controller/$action";
		$path=strtolower($path);
		
		if (in_array($controller, $arr_controller)) {
			if (in_array($action, $arr_action)) {
				$rbac=new Rbac;
				$status=$rbac->can($path);
				if ($status==false) {
					header("Content-Type:application/json");
					$arr=['code'=>'10001','status'=>'error','data'=>'没有权限!'];
					echo json_encode($arr);
					die;
				}
			}
		}
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);

        // incr() 对指定的key的值加1
        // decr()对指定的key的值减1
        // incrBy() 将第二个参数的值加到key的值上
        // decrBy() 将第二个参数的值加到key的值上
        // incrByFloat() 自增一个浮点类型的值

//        echo $redis->incr('shenzhen')."<br/>";//1
//        echo $redis->incr('shenzhen')."<br/>";//2
//        echo $redis->incrBy('shenzhen',6)."<br/>";//8
//        echo $redis->decr('shenzhen')."<br/>";//7
//        echo $redis->decr('shenzhen')."<br/>";//6
//        echo $redis->decrBY('shenzhen',3)."<br>"; //3
        $redis->incrByFloat('shenzhen',0.88);//3.88

	}
	public function token()
	{	
		$token = $this->request->token('__token__', 'sha1');
        $arr=['token'=>$token];
        echo json_encode($arr);
	}
}