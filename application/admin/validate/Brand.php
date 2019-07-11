<?php

namespace app\admin\validate;

use think\Validate;

class Brand extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'name'  =>  'require|token',
        'url' =>  'require',
        'is_show' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '品牌名称不能为空！',
        'url.require' => '品牌官网路径不能为空!',
        'is_show.require' => '请至少选中一个!',
    ];
}
