<?php

namespace app\admin\validate;

use think\Validate;

class Goodsspecs extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    // protected $regex = [ 'zip' => '/^[a-zA-Z\u4e00-\u9fa5]+$/'];
    protected $rule = [
        'goods_id' => 'require|token',
        'stock'  =>  "require|number",
        'price' => 'require|number',
//        'spe_attr_id' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'goods_id.require' => '商品ID不能为空！',
        // 'name.regex' => '商品名字不符合标准!',
        'stock.require' => '货品库存不能为空！',
        'price.require' => '货品价格不能为空！',
        'stock.number' => '货品库存只能是数字！',
        'price.number' => '货品价格只能是数字！',
//        'spe_attr_id.require' => '商品品牌不能为空!'
    ];
}
