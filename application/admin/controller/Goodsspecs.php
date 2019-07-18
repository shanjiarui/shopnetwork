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
        return $this->fetch('goodsspecs/goods_specs');
    }
    public function list()
    {
    	//需要查出来商品ID 货品ID 商品属性 商品单价 商品库存 商品ID不需要展示但是需要写在一个隐藏域中供添加使用
    }
}
