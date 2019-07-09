<?php
namespace app\admin\controller;
/**
 * 
 */
use think\Controller;
use gmars\rbac\Rbac;
use think\Db;
use Request;
use think\Validate;
use Session;
class User extends Common
{
	public function user()
	{
		return $this->fetch('user/show');
	}
	public function list()
	{
		$arr=Db::query('select u.id,u.user_name,u.mobile,r.id as rid,r.name from user as u join user_role as ur on u.id=ur.user_id join role as r on ur.role_id=r.id');
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function addUser()
	{
		$arr=Db::query('select * from role');
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function add_action()
	{
		$data=Request::post();
		$name=$data['name'];
		$password=$data['password'];
		$re_password=$data['re_password'];
		$phone=$data['phone'];
		$role_id=$data['role_id'];
		$validate = new \app\admin\validate\User;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        if ($re_password!=$password) {
        	$js=['code'=>'3','status'=>'error','data'=>'两次密码不一致!'];
        	echo json_encode($js);
        	die;
        }
        $arr=Db::query("select * from user where user_name='$name'");
        if (empty($arr)) {
        	Db::query("insert into user (user_name,password,mobile,last_login_time,status,create_time,update_time) values ('$name','$password','$phone',0,0,0,0)");
        	$arr=Db::query("select * from user where user_name='$name'");
        	$id=$arr[0]['id'];
        	Db::query("insert into user_role (user_id,role_id) values ($id,$role_id)");
        	$js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
        	echo json_encode($js);
        }else{
        	$js=['code'=>'1','status'=>'error','data'=>'此用户已存在!'];
        	echo json_encode($js);
        }
	}
	public function up_user()
	{
		$id=Request::post('id');
		$u_arr=Db::query("select * from user where id=$id");
		$ur_arr=Db::query("select * from user_role where user_id=$id");
		$r_id=$ur_arr[0]['role_id'];
		$r_arr=Db::query("select * from role where id=$r_id");
		$all_role=Db::query("select * from role");
		$js=['u_arr'=>$u_arr,'ur_arr'=>$ur_arr,'r_arr'=>$r_arr,'all_role'=>$all_role];
		echo json_encode($js);
	}
	public function up_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\User;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $name=$data['name'];
        // $phone=$data['mobile'];
        $id=$data['id'];
        $password=$data['password'];
        $role_id=$data['role_id'];
        $arr=Db::query("select * from user where user_name='$name'");
        if (empty($arr)) {
        	Db::query("update user set user_name='$name',password='$password',mobile='' where id=$id");
        	Db::query("delete from user_role where user_id=$id");
        	Db::query("insert into user_role (user_id,role_id) values ($id,$role_id)");
        	$js=['status'=>'ok','data'=>'修改成功!'];
        	echo json_encode($js);
        }else{
        	if ($arr[0]['id']==$id) {
        		Db::query("update user set user_name='$name',password='$password',mobile='' where id=$id");
	        	Db::query("delete from user_role where user_id=$id");
	        	Db::query("insert into user_role (user_id,role_id) values ($id,$role_id)");
	        	$js=['status'=>'ok','data'=>'修改成功!'];
	        	echo json_encode($js);
        	}else{
        		$js=['code'=>'3','status'=>'error','data'=>'此用户已存在!'];
        		echo json_encode($js);
        	}
        }
	}
	public function del_one()
	{
		$data=Request::post();
		$id=$data['id'];
		$validate = new \app\admin\validate\User_del;
		if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		Db::query("delete from user where id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
	}
	public function del_more()
	{
		$data=Request::post();
		$id=$data['id'];
		$validate = new \app\admin\validate\User_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$arr=explode(",", $id);
		for ($i=0; $i < count($arr); $i++) { 
			$aaa=$arr[$i];
			Db::query("delete from user where id=$aaa");
		}
		$js=['data'=>'ok'];
		echo json_encode($js);
	}
}