<?php

namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'name'  =>  'require|max:20|min:1',
        'description' => 'require|max:50'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '分组名不能为空！',
        'name.max' => '分组名不能大于20字节！',
        'description.require' => '分组描述不能为空!',
        'description.max' => '分组描述不能超过50字节!',
    ];
}
