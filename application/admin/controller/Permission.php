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
class Permission extends Common
{
	// public function initialize()
	// {
		
	// }
	public function permission()
	{
		$token=uniqid();
		Session::set('token',$token);
		$ayy=Db::query("select * from permission_category");
		$this->assign('arr',$ayy);
		$this->assign('token',$token);
		return $this->fetch('permission/permission');
	}
	public function list()
	{
		//查询所有的权限
		// $rbac=new Rbac();
		// $arr=$rbac->getPermission([]);
		//查询所有的权限和权限的分类名字
		$arr=Db::query("select p.path,p.id,p.name as p_name,pc.name,p.description from permission as p join permission_category as pc on p.category_id=pc.id");
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
    	$token=Request::post('token');
    	$my_description=Request::post('my_description');
    	$arr=Db::query("select * from permission where name='$name' or path='$path'");
    	//逻辑判断如果不修改权限名字是否是自己
    	//1.判断name和path在库中有没有重复的
    	//2.假如有重复的将他们的ID取出来跟接过来的ID进行比对
    	//3.六种可能  1 name相同但是ID不同  2 name相同且ID相等 3 path相同但ID不同 4 path相同且ID相同 5 name和path的ID都不相等  6 都相等
    	//4.修改成功的  2 4 6
    	if ($token!=Session::get('token')) {
		    		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!"];
		        	echo json_encode($js);
		        	die;
		    }
    	if (empty($arr)) {
				$arr=Db::query("update permission set name='$name',category_id=$category,path='$path',description='$my_description' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		    	echo json_encode($js);
    	}elseif (count($arr)==1) {
                if ($arr[0]['id']!=$id) {
                    if ($value['name']==$name) {
                        $js=['code'=>'0','status'=>'error','data'=>'权限名字已存在!'];
                        echo json_encode($js);
                        die;
                    }else{
                        $js=['code'=>'0','status'=>'error','data'=>'权限路径已存在!'];
                        echo json_encode($js);
                        die;
                    }
                }else{
                    $arr=Db::query("update permission set name='$name',category_id=$category,path='$path',description='$my_description' where id=$id");
                    $js=['code'=>'0','status'=>'ok','data'=>$arr];
                    echo json_encode($js);
                }
        }elseif (count($arr)==2) {
                $new_arr=[];
                for ($i=0; $i < 2; $i++) { 
                    if ($arr[$i]['id']!=$id) {
                        $new_arr[]=$arr[$i];
                    }
                }
                if (empty($new_arr)) {
                    $arr=Db::query("update permission set name='$name',category_id=$category,path='$path',description='$my_description' where id=$id");
                    $js=['code'=>'0','status'=>'ok','data'=>$arr];
                }elseif (count($new_arr)==1) {
                    if ($new_arr[0]['name']==$name) {
                        $js=['code'=>'0','status'=>'error','data'=>'权限名字已存在!'];
                        echo json_encode($js);
                        die;
                    }else{
                        $js=['code'=>'0','status'=>'error','data'=>'权限路径已存在!'];
                        echo json_encode($js);
                        die;
                    }
                }elseif (count($new_arr)==2) {
                        $js=['code'=>'0','status'=>'error','data'=>'权限名字已存在!'];
                        echo json_encode($js);
                        die;
                }
        }
    }
    public function del_permission()
    {
    	$token=Request::post('token');
    	$id=Request::post('id');
    	if ($token!=Session::get('token')) {
    		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!"];
        	echo json_encode($js);
        	die;
    	}
    	$arr=Db::query("delete from permission where id=$id");
    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
    	echo json_encode($js);
    }
    public function add_action()
    {
    	$name=Request::post('name');
    	$cate=Request::post('cate');
    	$path=Request::post('path');
    	$token=Request::post('token');
    	$description=Request::post('description');
    	if ($token!=Session::get('token')) {
    		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!"];
        	echo json_encode($js);
        	die;
    	}
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
		    'description' => $description,
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
    	$token=Request::post('token');
    	$arr_id=explode(",", $id);
    	if (empty($arr_id[1])) {
    		$js=['code'=>'0','status'=>'error','data'=>"请选择要删除的权限!"];
			echo json_encode($js);
			die;
    	}
    	if ($token!=Session::get('token')) {
    		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!"];
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
    public function new_update_action()
    {
    	$rbac=new Rbac;
    	$id=Request::post('id');
    	$name=Request::post('name');
    	$token=Request::post('token');
    	//此处的查询是用来判断如果没有修改那么久提交了会报错重名 用根据name查出来的ID与ajax传过来的id进行比对判断
    	$select_arr=$rbac->getPermission([['name', '=', $name]]);
    	//下面这个查询是用来判断修改是否成功 如果未成功将会查出来修改前的名字 将名字在json串中输出 方便在ajax页面替换span标签
    	$old_arr=$rbac->getPermission([['id', '=', $id]]);
    	//逻辑判断如果不修改权限名字是否是自己
	    	if (!empty($select_arr)&&$select_arr[0]['id']==$id) {
	    		$arr=Db::query("update permission set name='$name' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		    	echo json_encode($js);
	    	}elseif (!empty($select_arr)&&$select_arr[0]['id']!=$id) {
	    		$js=['code'=>'0','status'=>'error','data'=>'权限名字已存在！','old_name'=>$old_arr[0]['name']];
		    	echo json_encode($js);
	    	}elseif (empty($select_arr)) {
	    		//当所有验证都通过之后验证token
	    		if ($token!=Session::get('token')) {
		    		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!",'old_name'=>$old_arr[0]['name']];
		        	echo json_encode($js);
		        	die;
		    	}
	    		$arr=Db::query("update permission set name='$name' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		    	echo json_encode($js);
	    	}
    }	
    // public function test()
    // {
    // 	$a = array();
    // 	$b="1";
    // 	if ($b=="1") {
    // 		$a=["nihao","ok"];
    // 	}
    // 	var_dump($a);
    // }
}