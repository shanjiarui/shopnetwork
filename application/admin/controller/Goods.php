<?php
namespace app\admin\controller;
/**
 * 
 */

use Redis;
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
		$arr=Db::query('select g.goods_id,g.goods_name,sb.brand_name,sc.cate_name,g.is_show,g.goods_out,g.add_time from goods as g join shop_brand as sb on g.brand_id=sb.brand_id join shop_category as sc on g.cate_id=sc.cate_id limit 0,50');
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $hot=$redis->Zrevrange('sort',0,4,true);
		$js=['code'=>'0','status'=>'ok','data'=>$arr,'cate'=>$akk,'brand'=>$ayy,'hot'=>$hot];
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
            $redis = new Redis();
            $redis->connect('127.0.0.1',6379);
            $redis->del('select');
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
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->del('select');
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
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->del('select');
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
	public function minute()
	{
		$id=Request::get('id');
		$this->assign('id',$id);
		return $this->fetch("goods/minute");
	}
	public function show_all()
	{
		$id=Request::post('id');
		// echo "$id";die;
		$goods=Db::query("select * from goods where goods_id=$id");
		$attr_category=Db::query("select * from attr_category");
		$specific_attr=Db::query("select * from goods_attr where goods_id=$id");

		if (empty($specific_attr)){
            $js=['goods'=>$goods,'attr_category'=>$attr_category,'status'=>'300'];
            echo json_encode($js);
        }else{
            $attr_id=$specific_attr[0]['attr_id'];
            $attr_category_id=Db::query("select * from attr where id=$attr_id");
            $cate_id=$attr_category_id[0]['attr_category_id'];
            $arr=Db::query("select attr.name as a_name,specific_attr.id as spe_id,specific_attr.`name` from specific_attr join attr on attr.id=specific_attr.attr_id where attr.attr_category_id=$cate_id");
            $new_arr=[];
            foreach ($arr as $key => $value) {
                $new_arr[$value['a_name']][$value['spe_id']]=$value['name'];
            }
            $moren_attr=Db::query("select specific_attr.id from specific_attr join goods_attr on specific_attr.id=goods_attr.specific_attr_id where goods_attr.goods_id=$id");
            $js=['goods'=>$goods,'attr_category'=>$attr_category,'attr_id'=>$cate_id,'arr'=>$new_arr,'specific_attr'=>$specific_attr,'moren_id'=>$moren_attr,'status'=>'200'];
            echo json_encode($js);
        }

	}
	public function attr_cate()
	{
	    $goods_id=Request::post('goods_id');
		$id=Request::post('id');
		$arr=Db::query("select attr.name as a_name,specific_attr.id as spe_id,specific_attr.`name` from specific_attr join attr on attr.id=specific_attr.attr_id where attr.attr_category_id=$id");
		$new_arr=[];
		foreach ($arr as $key => $value) {
			$new_arr[$value['a_name']][$value['spe_id']]=$value['name'];
		}
        $specific_attr=Db::query("select specific_attr.id from specific_attr join goods_attr on specific_attr.id=goods_attr.specific_attr_id where goods_attr.goods_id=$goods_id");
		$js=['code'=>'0','status'=>'ok','data'=>$new_arr,'spe_id'=>$specific_attr];
		echo json_encode($js);

		
	}
	public function specific_attr()
	{
		$id=Request::post('id');
		// echo $id;die;
		if ($id=='') {
			$js=['code'=>'0','status'=>'error','data'=>'属性分类未选择'];
			echo json_encode($js);
		}else{
			$arr=Db::query("select * from specific_attr where attr_id=$id");
			$js=['code'=>'0','status'=>'ok','data'=>$arr];
			echo json_encode($js);
		}
		
	}
	public function up_attr()
	{
		$str=Request::post('str');
		$goods_id=Request::post('goods_id');
        $attr_cate_id=Request::post('attr_cate_id');
		$arr=Db::query("delete from goods_attr where goods_id=$goods_id");
		if ($str=='') {
			$js=['code'=>'0','status'=>'error','data'=>'属性分类未选择'];
			echo json_encode($js);
			die;
		}else{
		    Db::query("update goods set attr_cate_id=$attr_cate_id where goods_id=$goods_id");
			$ayy=explode(",",$str);
			for ($i=0; $i < count($ayy); $i++) { 
				$id=$ayy[$i];
				$okk=Db::query("select * from specific_attr where id=$id");
				$attr_id=$okk[0]['attr_id'];
				Db::query("insert into goods_attr(goods_id,specific_attr_id,attr_id) values ($goods_id,$id,$attr_id)");
			}
			$js=['code'=>'0','status'=>'ok','data'=>'属性添加成功!'];
			echo json_encode($js);
		}

	}
	public function my_sel()
    {
        $data=Request::post();
        $name=$data['name'];
        $sql="select g.goods_id,g.goods_name,sb.brand_name,sc.cate_name,g.is_show,g.goods_out,g.add_time from goods as g join shop_brand as sb on g.brand_id=sb.brand_id join shop_category as sc on g.cate_id=sc.cate_id where g.goods_name like '%$name%' limit 0,50";
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $hot=$redis->Zrevrange('sort',0,4);
        if ($name==''){
            $arr=Db::query($sql);
            $ayy=json_encode($arr);
            $js=['code'=>'0','status'=>'error','data'=>$arr,'hot'=>$hot];
            echo json_encode($js);
            die;
        }
        $number=$redis->Hget('select',$name);
        if ($number==''){
            $arr=Db::query($sql);

            $redis->Hset('select',$name,1);
            $redis->Zadd('sort',1,$name);
            $hot=$redis->Zrevrange('sort',0,4,true);
            $js=['code'=>'0','status'=>'ok','data'=>$arr,'hot'=>$hot];
            echo json_encode($js);
            die;
        }else{
            $new_number=$number+1;
            $redis->Zadd('sort',$new_number,$name);
            $redis->Hset('select',$name,$new_number);
            $hot=$redis->Zrevrange('sort',0,4,true);
            if (($number-1)>5){
                $a=$redis->Hget('list',$name);
                $new_arr=json_decode($a);
                $js=['code'=>'0','status'=>'ok','data'=>$new_arr,'hot'=>$hot];
                echo json_encode($js);
            }else{
                $arr=Db::query($sql);
                $ayy=json_encode($arr);
                $redis->Hset('list',$name,$ayy);
                $hot=$redis->Zrevrange('sort',0,4,true);
                $js=['code'=>'0','status'=>'ok','data'=>$arr,'hot'=>$hot];
                echo json_encode($js);
            }

        }
    }
}