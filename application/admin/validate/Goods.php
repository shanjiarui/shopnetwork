<?php

namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'name'  =>  'require|token',
        'cate_id' => 'require|number',
        'brand_id' => 'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '商品名称不能为空！',
        'cate_id.require' => '商品分类不能为空！',
        'brand_id.require' => '商品品牌不能为空!'
    ];
}
