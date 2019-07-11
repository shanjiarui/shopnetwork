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
class Permissioncate extends Common
{
	public function permissioncate()
	{
		return $this->fetch('permission_cate/permission_cate');
	}
	public function list($value='')
	{
		//查询所有的权限
		// $rbac=new Rbac();
		// $arr=$rbac->getPermission([]);
		//查询所有的权限和权限的分类名字
		$arr=Db::query("select * from permission_category");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function add_action()
    {
        $data=Request::post();
    	$name=$data['name'];
    	$description=$data['description'];
    	//传值过来先使用验证器进行验证，类似正则表达式
        $validate = new \app\admin\validate\Permissioncate;
        if (!$validate->check($data)) {
        	$js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        //判断库里是否已存在这个权限
    	$arr=Db::query("select * from permission_category where name='$name'");
    	if (!empty($arr)) {
    		$js=['code'=>'0','status'=>'error','data'=>"分组名称已存在！"];
    		echo json_encode($js);
    		die;
    	}
    	//判断库里是否已存在此权限路径
    	$ayy=Db::query("select * from permission_category where description='$description'");
    	if (!empty($ayy)) {
    		$js=['code'=>'0','status'=>'error','data'=>"分组描述已存在！"];
    		echo json_encode($js);
    		die;
    	}
        $rbac=new Rbac;
        $rbac->savePermissionCategory([
		    'name' => $name,
		    'description' => $description,
		    'status' => 1
		]);
		$js=['code'=>'0','status'=>'ok','data'=>"添加成功!"];
		echo json_encode($js);
    }
    public function del_permission_category()
    {

    	$data=Request::post();
    	$id=$data['id'];
        $validate = new \app\admin\validate\Product_del;
        if (!$validate->check($data)) {
            $js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
            echo json_encode($js);
            die;
        }
    	$arr=Db::query("delete from permission_category where id=$id");
    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
    	echo json_encode($js);
    }
    public function up_permission_category()
    {
    	$id=Request::post('id');
		$arr=Db::query("select * from permission_category where id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
    }
    public function up_permissioncate_action()
    {
    	$rbac=new Rbac();
        $data=Request::post();
    	$id=$data['id'];
    	$name=$data['name'];
    	// $__token__=Request::post('__token__');
    	$my_description=$data['my_description'];
        $validate = new \app\admin\validate\Permissioncate_up;
        if (!$validate->check($data)) {
            $js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
            echo json_encode($js);
            die;
        }
    	$arr=Db::query("select * from permission_category where name='$name'");
    	if (empty($arr)) {
    		$arr=Db::query("update permission_category set name='$name',description='$my_description' where id=$id");
	    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
	    	echo json_encode($js);
    	}else{
    		if ($arr[0]['id']==$id) {
    			$arr=Db::query("update permission_category set name='$name',description='$my_description' where id=$id");
		    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		    	echo json_encode($js);
    		}else{
    			$js=['code'=>'0','status'=>'error','data'=>'分组名称已存在!'];
		    	echo json_encode($js);
    		}
    	}
    }
    public function all_del()
    {
    	$id=Request::post('id');
    	$__token__=Request::post('__token__');
    	$arr_id=explode(",", $id);
    	if (empty($arr_id[1])) {
    		$js=['code'=>'0','status'=>'error','data'=>"请选择要删除的权限!"];
			echo json_encode($js);
			die;
    	}
    	for ($i=1; $i < count($arr_id) ; $i++) { 
    		$id=$arr_id[$i];
    		Db::query("delete from permission_category where id=$id");
    	}
    	$js=['code'=>'0','status'=>'ok','data'=>"删除成功!"];
		echo json_encode($js);
    }
    // 	if ($__token__!=Session::get('__token__')) {
		  //   		$js=['code'=>'120','status'=>'error','data'=>"令牌验证失败!"];
		  //       	echo json_encode($js);
		  //       	die;
		  //   }
    // 	if (empty($arr)) {
				// $arr=Db::query("update permission_category set name='$name',description='$my_description' where id=$id");
		  //   	$js=['code'=>'0','status'=>'ok','data'=>$arr];
		  //   	echo json_encode($js);
    // 	}else{
    // 		foreach ($arr as $key => $value) {
    // 			if ($value['id']!=$id) {
    // 				if ($value['name']==$name) {
    // 					$js=['code'=>'0','status'=>'error','data'=>'分类名字已存在!'];
		  //   			echo json_encode($js);
		  //   			die;
    // 				}else{
    // 					$js=['code'=>'0','status'=>'error','data'=>'分类描述已存在!'];
		  //   			echo json_encode($js);
		  //   			die;
    // 				}
    // 			}else{
	   //  			$arr=Db::query("update permission_category set name='$name',description='$my_description' where id=$id");
			 //    	$js=['code'=>'0','status'=>'ok','data'=>$arr];
			 //    	echo json_encode($js);
    // 			}
    // 		}
    	// }
}