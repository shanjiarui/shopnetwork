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
class Role extends Common
{
	public function role()
	{

		return $this->fetch('role/role');
	}
	public function list()
	{
		$arr=Db::query('select * from role');
		$js=['code'=>'0','status'=>'1','data'=>$arr];
		echo json_encode($js);
	}
	public function role_add()
	{
		
		return $this->fetch('role/role_add');
	}
	public function all_permission()
	{
		$arr=Db::query('select p.id as p_id,pc.id,p.name as p_name,pc.name,p.category_id from permission as p join permission_category as pc on p.category_id=pc.id');
		$new_arr=[];
		foreach ($arr as $key => $value) {
			$new_arr[$value['name']][$value['p_id']]=$value['p_name'];
		}
		$js=["data"=>$new_arr];
		echo json_encode($js);
	}
	public function role_add_action()
	{
		$rbac=new Rbac;
		$data=Request::post();
		$all_permission=explode(",",$data['permission']);
		$name=$data['name'];
		$description=$data['description'];
		$arr=Db::query("select * from role where name='$name'");
		unset($all_permission[0]);
		$validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		if (empty($all_permission)) {
			$js=['code'=>'0','status'=>'error','data'=>'请选择权限!'];
			echo json_encode($js);
			die;
		}
		$all_permission=implode(",", $all_permission);
		if (empty($arr)) {
			$rbac->createRole([
			    'name' => $name,
			    'description' => $description,
			    'status' => 1
			], $all_permission);
			$js=['code'=>'1','status'=>'ok','data'=>'添加成功!'];
			echo json_encode($js);
		}else{
			$js=['code'=>'1','status'=>'error','data'=>'此角色已经存在!'];
			echo json_encode($js);
		}
	}
	public function up_role()
	{
		//
		$id=Request::post('id');
		$role=Db::query("select * from role where id=$id");
		
		$role_permission=Db::query("select * from role_permission where role_id=$id");
		$js=['code'=>'0','status'=>'1','role'=>$role,'role_permission'=>$role_permission];
		echo json_encode($js);
	}
	public function up_role_action()
	{
		$data=Request::post();
		$id=$data['id'];
		$name=$data['name'];
		$description=$data['description'];
		$permission=$data['permission'];
		$validate = new \app\admin\validate\Role;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $arr_p=explode(",", $data['permission']);
        $arr_name=Db::query("select * from role where id=$id");
        if (empty($arr_name)) {
        	Db::query("update role set name='$name',description='$description' where id=$id");
        }elseif($arr_name[0]['id']==$id) {
        	Db::query("update role set name='$name',description='$description' where id=$id");
        }elseif ($arr_name[0]['id']!=$id) {
        	$js=['code'=>'0','status'=>'error','data'=>'此用户已存在!'];
        	echo json_encode($js);
        	die;
        }
        if ($arr_p[0]=='') {
        	$js=['code'=>'1','status'=>'error','data'=>'请至少勾选一个权限!'];
        	echo json_encode($js);
        	die;
        }else{
        	$arr=Db::query("select * from role_permission where role_id=$id");
        	foreach ($arr as $key => $value) {
        		$del_id=$value['id'];
        		Db::query("delete from role_permission where id=$del_id");

        	}
        	foreach ($arr_p as $key => $value) {
        		Db::query("insert into role_permission (role_id,permission_id) values ($id,$value)");
        	}
        	$js=['code'=>'2','status'=>'ok','data'=>'修改成功!'];
        	echo json_encode($js);
        }
	}
	public function del_role()
    {
    	$data=Request::post();
    	$validate = new \app\admin\validate\Role_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
    	$arr=Db::query("delete from role where id=$id");
    	$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
    	echo json_encode($js);
    }
    public function all_del()
    {
    	$data=Request::post();
    	$arr_id=explode(",", $data['id']);
    	$validate = new \app\admin\validate\Role_del_more;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
    	if (empty($arr_id)) {
    		$js=['code'=>'0','status'=>'error','data'=>"请选择要删除的权限!"];
			echo json_encode($js);
			die;
    	}
    	for ($i=0; $i < count($arr_id) ; $i++) { 
    		$id=$arr_id[$i];
    		Db::query("delete from role where id=$id");
    	}
    	$js=['code'=>'0','status'=>'ok','data'=>"删除成功!"];
		echo json_encode($js);
    }
}