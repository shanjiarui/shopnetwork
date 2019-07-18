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
class Attrspe extends Common
{
	public function Attrspe()
	{
		$attr_id=Request::get('attr_id');
		$this->assign('attr_id',$attr_id);
		return $this->fetch("attrspe/attrspe");
	}
	public function list()
	{
		$data=Request::post();
		$attr_id=$data['attr_id'];
		$arr=Db::query("select * from specific_attr where attr_id=$attr_id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function add_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Attrspe;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$attr_id=$data['attr_id'];
		$name=$data['name'];
		$arr=Db::query("select * from specific_attr where name='$name'");
		if (empty($arr)) {
			Db::query("insert into specific_attr(`name`,attr_id) values ('$name',$attr_id)");
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
        Db::query("delete from specific_attr where id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
	}
}