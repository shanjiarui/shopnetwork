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
class Goods extends Common
{
	public function Goods()
	{
		
		return $this->fetch('goods/goods');
	}
	public function list()
	{
		$ayy=Db::query('select * from shop_brand');
		$akk=Db::query('select * from shop_category');
		$arr=Db::query('select g.goods_id,g.goods_name,sb.brand_name,sc.cate_name,g.is_show,g.goods_out,g.add_time from goods as g join shop_brand as sb on g.brand_id=sb.brand_id join shop_category as sc on g.cate_id=sc.cate_id');
		$js=['code'=>'0','status'=>'ok','data'=>$arr,'cate'=>$akk,'brand'=>$ayy];
		echo json_encode($js);
	}
	public function add_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Goods;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$name=$data['name'];
		$cate_id=$data['cate_id'];
		$brand_id=$data['brand_id'];
       	$time=date('Y-m-d h:i:s', time());
		$arr=Db::query("select * from goods where goods_name='$name'");
		if (empty($arr)) {
			Db::query("insert into goods(goods_name,cate_id,brand_id,is_show,goods_out,add_time) values ('$name',$cate_id,$brand_id,0,0,'$time')");
			$js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
			echo json_encode($js);
		}else{
			$js=['code'=>'0','status'=>'ok','data'=>'此商品已存在!'];
			echo json_encode($js);
		}
	}
	public function del_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
        $arr=Db::query("select * from goods_photo where goods_id=$id");
        foreach ($arr as $key => $value) {
        	unlink("./static/img/".$value['small_img']);
        	unlink("./static/img/".$value['middle_img']);
        	unlink("./static/img/".$value['big_img']);
        	unlink("./static/img/".$value['img']);
        }
        Db::query("delete from goods where goods_id=$id");
        $js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
        echo json_encode($js);
	}
	public function select_all()
	{
		$id=Request::post('id');
		$arr=Db::query("select * from goods where goods_id=$id");
		$ayy=Db::query("select * from shop_brand");
		$akk=Db::query("select * from shop_category");
		$js=['code'=>'0','status'=>'ok','data'=>$arr,'brand'=>$ayy,'cate'=>$akk];
		echo json_encode($js);
	}
	public function up_show()
	{
		$data=Request::post();
		$is_show=$data['value'];
		$id=$data['id'];
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        if ($data['value']==1) {
        	Db::query("update goods set is_show=0 where goods_id=$id");
        	$js=['code'=>'0','status'=>'ok'];
        	echo json_encode($js);
        }else{
        	Db::query("update goods set is_show=1 where goods_id=$id");
        	$js=['code'=>'0','status'=>'ok'];
        	echo json_encode($js);
        }
	}
	public function up_action()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Goods_up;
        if (!$validate->check($data)) {
        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $name=$data['name'];
        $id=$data['id'];
        $cate_id=$data['cate'];
        $brand_id=$data['brand'];
        $arr=Db::query("select * from goods where goods_name='$name'");
        if (empty($arr)) {
        	Db::query("update goods set goods_name='$name',cate_id=$cate_id,brand_id=$brand_id where goods_id=$id");
        	$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
        	echo json_encode($js);
        }else{
        	if ($arr[0]['goods_id']==$id) {
        		Db::query("update goods set goods_name='$name',cate_id=$cate_id,brand_id=$brand_id where goods_id=$id");
	        	$js=['code'=>'0','status'=>'ok','data'=>'修改成功!'];
	        	echo json_encode($js);
        	}else{
        		$js=['code'=>'0','status'=>'ok','data'=>'修改失败!'];
	        	echo json_encode($js);
        	}
        }
	}
	public function goods_img()
	{
		$id=Request::get('id');
		$this->assign('id',$id);
		return $this->fetch('goods/goods_img');
	}
	public function add_img()
	{
		$files = request()->file('myfile');
		// var_dump($files);
		$data = Request::post();
		// var_dump($data);die;
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
	    foreach($files as $file => $value){
	        // 移动到框架应用根目录/uploads/ 目录下
	        $info = $value->validate(['size'=>1024*1024,'ext'=>'jpg,jpeg,png,gif'])->move( 'static/img');
	        if($info){
	        	$date=date("Ymd");
	        	$img = $info->getFilename();
	        	// echo $img;
	        	$image = \think\Image::open("static/img/$date/$img");
	        	$old_img=$date."/".$img;
	        	$big_img= $date."/big".$img;
	        	$image->thumb(120,120)->save("static/img/".$big_img);
	        	$middle_img= $date."/middle".$img;
	        	$image->thumb(90,90)->save("static/img/".$middle_img);
	        	$small_img= $date."/small".$img;
	        	$image->thumb(60,60)->save("static/img/".$small_img);
	        	// $str = $info->getSaveName();
	            // $str= str_replace("\\","/",$str);
	            // 成功上传后 获取上传信息
	            // 输出 jpg
	            // $info->getExtension(); 
	            // 输出 42a79759f284b767dfcb2a0197904287.jpg
	            $id=$data['id'];
	             Db::query("insert into goods_photo(goods_id,small_img,middle_img,big_img,img) values ($id,'$small_img','$middle_img','$big_img','$old_img')");
	            
	        }else{
	            // 上传失败获取错误信息
	            echo $files->getError();
	           
	        }    
	    }
	   
    	$js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
    	echo json_encode($js);
	}
	public function img_show()
	{
		$id=Request::post("id");
		$arr=Db::query("select goods_photo.id,goods.goods_name,goods_photo.small_img,goods_photo.middle_img,goods_photo.big_img from goods join goods_photo on goods.goods_id=goods_photo.goods_id where goods_photo.goods_id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}
	public function del_img()
	{
		$data=Request::post();
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
        $id=$data['id'];
        $arr=Db::query("select * from goods_photo where id=$id");
        foreach ($arr as $key => $value) {
        	unlink("./static/img/".$value['small_img']);
        	unlink("./static/img/".$value['middle_img']);
        	unlink("./static/img/".$value['big_img']);
        	unlink("./static/img/".$value['img']);
        }
		Db::query("delete from goods_photo where id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
		echo json_encode($js);
	}
}