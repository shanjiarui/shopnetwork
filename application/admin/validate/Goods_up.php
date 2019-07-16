<?php

namespace app\admin\validate;

use think\Validate;

class Goods_up extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    // protected $regex = [ 'zip' => '/^[a-zA-Z\u4e00-\u9fa5]+$/'];
	protected $rule = [ 
		'id' => 'require|token',
        'name'  =>  "require",
        'cate' => 'require',
        'brand' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
    	'id' => '商品ID不能为空！',
    	// 'name.regex' => '商品名字不符合标准!',
        'name.require' => '商品名称不能为空！',
        'cate.require' => '商品分类不能为空！',
        'brand.require' => '商品品牌不能为空!'
    ];
}
