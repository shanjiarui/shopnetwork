<?php

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [ 
        'name'  =>  'require|max:20|min:5|token',
        'password' => 'require|max:20|min:1',
        'phone'=>'number|length:11',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'name.require' => '姓名不能为空！',
        'name.max' => '姓名不能大于20字节！',
        'name.min' => '姓名不能小于5字节！',
        'password.require' => '密码不能为空!',
        'password.max' => '密码不能超过20字节!',
        'password.min' => '密码不能小于1字节!',
        'phone.number'=>'手机号必须是纯数字!',
        'phone.length'=>'手机号必须是11位!',
    ];
}
