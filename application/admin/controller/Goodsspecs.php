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
class Goodsspecs extends Common
{
    public function goodsspecs()
    {
        $id=Request::get('id');
        $arr=Db::query("select * from goods where goods_id=$id");
        $cate_id=$arr[0]['attr_cate_id'];
        $attr_id=Db::query("select * from attr where attr_category_id=$cate_id");
        $new=[];
        foreach ($attr_id as $key => $value){
            $new[]=$value['id'];
        }
        $str=implode(",",$new);
        $this->assign('attr_id',$str);
        $this->assign('id',$id);
        return $this->fetch('goodsspecs/goods_specs');
    }
    public function list()
    {
    	//需要查出来商品ID 货品ID 商品属性 商品单价 商品库存 商品ID不需要展示但是需要写在一个隐藏域中供添加使用
        $goods_id=Request::post('goods_id');
//        echo $goods_id;
        $all_attrspe_name=[];
//        $all=[];
        $arr=Db::query("select * from goods_specs where goods_id=$goods_id");
        for ($i=0;$i<count($arr);$i++){
            $specific_attr_id=$arr[$i]['goods_attr_id'];
            $specs_id=$arr[$i]['id'];
            $arr_attrspe=explode("-",$specific_attr_id);
//            var_dump($arr_attrspe);
            for($j=0;$j<count($arr_attrspe);$j++){
                $spe_id=$arr_attrspe[$j];
                $all_attrspe=Db::query("select * from specific_attr where id=$spe_id");
                $all_attrspe_name[]=$all_attrspe[0]['name'];
            }
            $spe_name=implode("-",$all_attrspe_name);
            $arr[$i]['goods_attr_id']=$spe_name;
            foreach ($all_attrspe_name as $key => $value){
                unset($all_attrspe_name[$key]);
            }
        }
        $goods=Db::query("select * from goods where goods_id=$goods_id");
        $cate_id=$goods[0]['attr_cate_id'];
        $attr=Db::query("select attr.id as a_id,attr.name as a_name,specific_attr.id,specific_attr.name from attr join specific_attr on specific_attr.attr_id=attr.id where attr.attr_category_id=$cate_id");
        $spe_attr_select=[];
        foreach ($attr as $key => $value){
            $spe_attr_select[$value['a_name']][$value['a_id']][$value['id']]=$value['name'];
        }
        $js=['code'=>'0','status'=>'ok','data'=>$arr,'sel'=>$spe_attr_select];
        return json($js);
    }
    function add_action(){
        $data=Request::post();
        $validate = new \app\admin\validate\Goodsspecs;
        if (!$validate->check($data)) {
            $js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
            return $js;
            die;
        }
        $goods_id=Request::post("goods_id");
        $stock=Request::post("stock");
        $price=Request::post("price");
        $spe_attr_id=Request::post("new_arr");
//        var_dump($spe_attr_id);
//        die;

        if (empty($spe_attr_id)){
            $js=['code'=>'0','status'=>'error','data'=>'请至少选择一个属性'];
            return json($js);
            die;
        }else{
            $spe_attr=implode("-",$spe_attr_id);
//            echo $spe_attr;
            $opo=Db::query("select * from goods_specs where goods_attr_id='$spe_attr'");
//            var_dump($opo);
            if (empty($opo)){
                Db::query("insert into goods_specs(goods_id,goods_attr_id,price,stock) values ($goods_id,'$spe_attr',$price,$stock)");
                $js=['code'=>'0','status'=>'ok','data'=>'添加成功!'];
                return json($js);
            }else{
                $js=['code'=>'0','status'=>'error','data'=>'此货品已存在!'];
                return json($js);
            }
        }
    }
    function del_one(){
        $data=Request::post();
        $validate = new \app\admin\validate\Brand_del;
        if (!$validate->check($data)) {
            $js=['code'=>'120','status'=>'error','data'=>$validate->getError()];
            return $js;
            die;
        }
        $id=Request::post('id');
        Db::query("delete from goods_specs where id=$id");
        $js=['code'=>'0','status'=>'ok','data'=>'删除成功!'];
        return $js;
    }
}
