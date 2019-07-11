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
class Brand extends Common
{
	public function brand()
	{
		return $this->fetch('brand/brand');
	}

	public function list()
	{
		$arr=Db::query('select * from shop_brand');
		$js=['data'=>$arr];
		echo json_encode($js);
	}

	public function select_all()
	{
		$data=Request::post();
		$id=$data['id'];
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$arr=Db::query("select * from shop_brand where brand_id=$id");
		$js=['data'=>$arr];
		echo json_encode($js);
	}

	public function up_action()
	{
		$data=Request::post();
		// var_dump($data);
		$id=$data['id'];
		$name=$data['name'];
		$url=$data['url'];
		$is_show=$data['is_show'];
		Db::query("update shop_brand set brand_name='$name',brand_url='$url',is_show=$is_show where brand_id=$id");
		$js=['code'=>'1','status'=>'ok','data'=>'修改成功!'];
		echo json_encode($js);
	}

	public function add_action()
	{
		
		$file = request()->file('myfile');
		// echo $file;
	    // 移动到框架应用根目录/uploads/ 目录下
	    if (empty($file)) {
	    	$js=['code'=>'0','status'=>'error','data'=>'请至少选择一个图片!'];
	    	die;
	    }
	    $info = $file->move( 'static/img');
	    if($info){
	    	$name=Request::post('name');
			$is_show=Request::post('is_show');
			$url=Request::post('url');
			$__token__=Request::post('__token__');
			$data=[
				'name'=>$name,
				'is_show'=>$is_show,
				'url'=>$url,
				'__token__'=>$__token__
				];
			$validate = new \app\admin\validate\Brand;
	        if (!$validate->check($data)) {
	        	$js=['code'=>'0','status'=>'error','data'=>$validate->getError()];
	        	echo json_encode($js);
	        	die;
	        }
	        // 成功上传后 获取上传信息
	        // 输出 jpg
	        // echo $info->getExtension();
	        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	        $str= $info->getSaveName();
	        // 输出 42a79759f284b767dfcb2a0197904287.jpg
	        // echo $info->getFilename(); 
	        $str= str_replace("\\","/",$str);
	        // echo "insert into shop_brand(brand_name,brand_logo,brand_url,is_show) values ('$name','$str','$url',$is_show)";
	        // die;
	        Db::query("insert into shop_brand(brand_name,brand_logo,brand_url,is_show) values ('$name','$str','$url',$is_show)");
	        $js=['code'=>'1','status'=>'ok','data'=>'添加成功!'];
	        echo json_encode($js);
	    }else{
	        // 上传失败获取错误信息
	        echo $file->getError();
	    }
	    die;
		
	}

	public function del_one()
	{
		$data=Request::post();
		$id=$data['id'];
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		Db::query("delete from shop_brand where brand_id=$id");
		$js=['code'=>'1','status'=>'ok','data'=>'删除成功!'];
        echo json_encode($js);
	}

	public function up_img()
	{
		$data=Request::post();
		$id=$data['id'];
		$validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
        	$js=['code'=>'4','status'=>'error','data'=>$validate->getError()];
        	echo json_encode($js);
        	die;
        }
		$arr=Db::query("select * from shop_brand where brand_id=$id");
		$js=['code'=>'0','status'=>'ok','data'=>$arr];
		echo json_encode($js);
	}

	public function up_img_action()
	{
		// ($_FILES["file"]);  
	    $name = $_FILES["file"]["name"];
	    $arr=explode('.', $name);
	    $houzhui=end($arr);
	    $file_name=uniqid().'.'.$houzhui;
	     //中文可能乱码使用iconv函数  
	    $bool=move_uploaded_file($_FILES["file"]["tmp_name"],"./static/img/".$file_name);   
	    if ($bool==true) {
	    	$id=Request::post('id');
	    	$arr=Db::query("select * from shop_brand where brand_id=$id");
	    	$logo=$arr[0]['brand_logo'];
	    	unlink("./static/img/".$logo);
	    	Db::query("update shop_brand set brand_logo='$file_name' where brand_id=$id");
	   		echo "ok";
	    }else{
	    	echo "no";
	    }
	}
}