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
class Permission extends Common
{
	public function permission()
	{
		$ayy=Db::query("select * from permission_category");
		$this->assign('arr',$ayy);
		return $this->fetch('permission/permission');
	}
	public function list()
	{
		//查询所有的权限
		// $rbac=new Rbac();
		// $arr=$rbac->getPermission([]);
		//查询所有的权限和权限的分类名字
		$arr=Db::query("select p.path,p.id,p.name as p_name,pc.name from permission as p join permission_category as pc on p.category_id=pc.id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function up_permission()
	{
		$id=Request::post('id');
		$arr=Db::query("select * from permission where id=$id");
		$ayy=Db::query("select * from permission_category");
		$js=['code'=>'0','status'=>'ok','data'=>$arr,'cate'=>$ayy];
		echo json_encode($js);
	}
	public function up_permission_action()
    {
    	$rbac=new Rbac();
    	$id=Request::post('id');
    	$name=Request::post('name');
    	$path=Request::post('path');
    	$category=Request::post('category');
    	$select_arr=$rbac->getPermission([['name', '=', $name]]);
    	//逻辑判断如果不修改权限名字是否是自己
	    	if (!empty($select_arr)&&$select_arr[0]['id']==$id) {
	    		$arr=Db::query("update permission set name='$name',category_id=$category,path='$path' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		    	echo json_encode($js);
	    	}elseif (!empty($select_arr)&&$select_arr[0]['id']!=$id) {
	    		$js=['code'=>'0','status'=>'error','data'=>'权限名字已存在！'];
		    	echo json_encode($js);
	    	}elseif (empty($select_arr)) {
	    		$arr=Db::query("update permission set name='$name',category_id=$category,path='$path' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
	    	}
    }
    public function del_permission()
    {
    	$id=Request::post('id');
    	$arr=Db::query("delete from permission where id=$id");
    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
    	echo json_encode($js);
    }
    public function add_action()
    {
    	$name=Request::post('name');
    	$cate=Request::post('cate');
    	$path=Request::post('path');
    	//传值过来先使用验证器进行验证，类似正则表达式
     	 $data = [
            'name'  => $name,
            'path' => $path,
            'cate' => $cate
        ];
        $validate = new \app\admin\validate\Permission;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        //判断库里是否已存在这个权限
    	$arr=Db::query("select * from permission where name='$name'");
    	if (!empty($arr)) {
    		$js=['code'=>'0','status'=>'error','data'=>"权限名称已存在！"];
    		echo json_encode($js);
    		die;
    	}
    	//判断库里是否已存在此权限路径
    	$ayy=Db::query("select * from permission where path='$path'");
    	if (!empty($ayy)) {
    		$js=['code'=>'0','status'=>'error','data'=>"权限路径已存在！"];
    		echo json_encode($js);
    		die;
    	}
        $rbac=new Rbac;
        $arr=$rbac->createPermission([
		    'name' => $name,
		    'description' => '文章列表查询',
		    'status' => 1,
		    'type' => 1,
		    'category_id' => $cate,
		    //权限路径不能重复 否则报错说权限路径为空
		    'path' => $path,
		]);
		$js=['code'=>'0','status'=>'ok','data'=>"添加成功!"];
		echo json_encode($js);
    }
    public function all_del()
    {
    	$id=Request::post('id');
    	$arr_id=explode(",", $id);
    	if (empty($arr_id[1])) {
    		$js=['code'=>'0','status'=>'error','data'=>"请选择要删除的权限!"];
			echo json_encode($js);
			die;
    	}
    	for ($i=1; $i < count($arr_id) ; $i++) { 
    		$id=$arr_id[$i];
    		Db::query("delete from permission where id=$id");
    	}
    	$js=['code'=>'0','status'=>'ok','data'=>"删除成功!"];
		echo json_encode($js);
    }
    public function new_update()
    {
    	$id=Request::post('id');
    	$arr=Db::query("select * from permission where id=$id");
    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
    }
}