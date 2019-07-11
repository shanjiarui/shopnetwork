<?php

namespace app\admin\validate;

use think\Validate;

class Brand_del extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'id'  =>  'require|token',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'id.require' => 'id不能为空！',
        // // 'name.max' => '权限内容不能大于20字节！',
        // 'url.require' => '品牌官网路径不能为空!',
        // // 'logo.require' => '品牌Logo不能为空!',
        // // 'path.max' => '权限地址不能超过50字节!',	
        // // 'path.min' => '权限地址不能少于5字节!',
        // // 'path.ip' => '权限地址路径格式错误!',
        // 'is_show.require' => '请至少选中一个!'
    ];
}
