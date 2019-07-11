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
class Product extends Common
{
	public function product()
	{
		return $this->fetch('product/product');
		
	}
	// public function brand()
	// {
		
	// }
	public function tree()
	{
		$arr=Db::query("select * from shop_category");
		$this->getTree($arr);
	}
	public function getTree($array, $pid =0, $level = 0){

	    // $f_name=__FUNCTION__; // 定义当前函数名

	    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
	    static $list = [];
	    echo "<ul>";
	    foreach ($array as $key => $value){
	        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
	        if ($value['parent_id'] == $pid){
	            //父节点为根节点的节点,级别为0，也就是第一级
	            // $flg = str_repeat('',$level);
	            echo "<li value='{$value['cate_id']}'>{$value['cate_name']}     <button class='btn btn-warning radius' style='width:25px;height:22px;font-size:5px;vertical-align:middle;padding:0px;' onclick='my_update({$value['cate_id']})'>修改</button><button class='btn btn-danger radius' style='width:25px;height:22px;font-size:5px;vertical-align:middle;padding:0px;' onclick='del_one({$value['cate_id']})'>删除</button></li>";
	            // 更新 名称值
	            // $value['cate_name'] = $flg.$value['cate_name'];
	            // 输出 名称
	            // echo $value['ncate_name']."<br/>";
	            //把数组放到list中
	            $list[] = $value;
	            //把这个节点从数组中移除,减少后续递归消耗
	            unset($array[$key]);
	            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
	            $this->getTree($array, $value['cate_id'], $level+1);
	        }
	    }
	    echo "</ul>";
	    // return $list;
	}
	public function add_action()
	{
		$data=Request::post();
		$p_id=$data['p_id'];
		$name=$data['name'];
		$validate = new \app\admin\validate\Product;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $arr=Db::query("select * from shop_category where cate_name='$name'");
        if (empty($arr)) {
        	Db::query("insert into shop_category(cate_name,parent_id) values ('$name','$p_id')");
        	$js=['code'=>'1','status'=>'ok','data'=>'添加成功!!'];
        	echo json_encode($js);
        }else{
        	$js=['code'=>'2','status'=>'error','data'=>'此分类已存在!'];
        	echo json_encode($js);
        }
	}
	public function del_one()
	{
		$data=Request::post();
		$id=$data['id'];
		Db::query("delete from shop_category where cate_id=$id");
		$validate = new \app\admin\validate\Product_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$js=$this->del_action($id);
		$js=['code'=>'1','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
		
	}
	function del_action($id)
	{
		$arr=Db::query("select * from shop_category where parent_id=$id");

		if (empty($arr)) {
			// $js=['code'=>'1','status'=>'ok','data'=>'删除成功!'];
			// return $js;
		}else{
			foreach ($arr as $key => $value) {
				$cate_id=$value['cate_id'];
				Db::query("delete from shop_category where cate_id=$cate_id");
				$this->del_action($value['cate_id']);
			}
		}
	}
	public function my_update()
	{
		$id=Request::post('id');
		$arr=Db::query("select * from shop_category where cate_id=$id");
		$ayy=Db::query("select * from shop_category");
		$js=['code'=>'0','status'=>'ok','data'=>$arr,'all'=>$ayy];
		echo json_encode($js);
	}
	public function up_action()
	{
		$id=Request::post('id');
		$name=Request::post('name');
		$p_id=Request::post('p_id');
		$__token__=Request::post('__token__');
		$data=['name'=>$name,'p_id'=>$p_id,'__token__'=>$__token__];
		$validate = new \app\admin\validate\Product;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$arr=Db::query("select * from shop_category where cate_id=$id");
		if (empty($arr)) {
			Db::query("update shop_category set cate_name='$name',parent_id='$p_id' where cate_id=$id");
			$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
			echo json_encode($js);
		}else{
			if ($arr[0]['cate_id']==$id) {
				Db::query("update shop_category set cate_name='$name',parent_id='$p_id' where cate_id=$id");
				$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
				echo json_encode($js);
			}else{
				$js=['code'=>'0','status'=>'error','data'=>'此分类名已经存在!'];
				echo json_encode($js);
			}
		}
	}
}