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
class Attrcate extends Common
{
	public function attrCate()
	{
		return $this->fetch("attrcate/attr_cate");
	}
	public function list()
	{
		$arr=Db::query("select * from attr_category");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function add_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Attrcate_add;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$name=$data['name'];
		$arr=Db::query("select * from attr_category where name='$name'");
		if (empty($arr)) {
			Db::query("insert into attr_category(`name`) values ('$name')");
			$js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
			echo json_encode($js);
		}else{
			$js=['code'=>'0','status'=>'ok','data'=>'此用户已存在!'];
			echo json_encode($js);
		}
	}
	public function del_one()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$id=$data['id'];
		Db::query("delete from attr_category where id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
	}
	public function up_attrcate()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
        $arr=Db::query("select * from attr_category where id=$id");
        $js=['code'=>'0','status'=>'ok','data'=>$arr];
        echo json_encode($js);
	}
	public function up_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Attrcate_add_action;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
        $name=$data['name'];
        $arr=Db::query("select * from attr_category where name='$name'");
        if (empty($arr)) {
        	Db::query("update attr_category set name='$name' where id=$id");
        	$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
        	echo json_encode($js);
        }else{
        	if ($arr[0]['id']==$id) {
        		Db::query("update attr_category set name='$name' where id=$id");
	        	$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
	        	echo json_encode($js);
        	}else{
        		$js=['code'=>'0','status'=>'error','data'=>'此属性分类已经存在!'];
	        	echo json_encode($js);
        	}
        }
	}
}