<?php

namespace app\admin\validate;

use think\Validate;

class Attrspe extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
		'attr_id' => 'require|token',
        'name'  =>  'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '属性名称不能为空！',
     
    ];
}
