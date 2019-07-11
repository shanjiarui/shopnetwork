<?php
namespace app\admin\controller;
/**
 * 
 */
use think\Controller;
use gmars\rbac\Rbac;
use think\Db;
use Request;
use think\Validate;
use Session;
class Productlist extends Common
{
	public function Productlist()
	{
		return $this->fetch('productlist/productlist');
	}
}