<?php

namespace app\admin\validate;

use think\Validate;

class Permission extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'name'  =>  'require|max:20|min:1',
        'path' =>  'require|max:50|min:5',
        'cate' => 'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '权限内容不能为空！',
        'name.max' => '权限内容不能大于20字节！',
        'path.require' => '权限地址不能为空!',
        'path.max' => '权限地址不能超过50字节!',
        'path.min' => '权限地址不能少于5字节!',
        // 'path.ip' => '权限地址路径格式错误!',
        'cate.require' => '权限分类不能为空!'
    ];
}
