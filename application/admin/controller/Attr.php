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
class Attr extends Common
{
	public function attr()
	{
		$attr_cate_id=Request::get('attr_cate');
		// echo $attr_cate_id;die;
		$this->assign('attr_cate_id',$attr_cate_id);
		return $this->fetch('attr/attr');
	}
	public function list()
	{
		$data=Request::post();
		$attr_cate_id=$data['attr_cate_id'];
		$arr=Db::query("select * from attr where attr_category_id=$attr_cate_id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function add_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Attradd;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$attr_cate_id=$data['attr_cate_id'];
		$name=$data['name'];
		$arr=Db::query("select * from attr where name='$name'");
		if (empty($arr)) {
			Db::query("insert into attr(`name`,attr_category_id) values ('$name',$attr_cate_id)");
			$js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
			echo json_encode($js);
		}else{
			$js=['code'=>'0','status'=>'error','data'=>'此属性已经存在!'];
			echo json_encode($js);
		}
	}
	public function del_one()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
        Db::query("delete from attr where id=$id");
        Db::query("delete from specific_attr where attr_id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
	}
}