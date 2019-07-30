<?php

namespace app\admin\validate;

use think\Validate;

class Goodssel extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    // protected $regex = [ 'zip' => '/^[a-zA-Z\u4e00-\u9fa5]+$/'];
    protected $rule = [
        'name'  =>  "require|token",
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [

        'name.require' => '商品名称不能为空！',
    ];
}