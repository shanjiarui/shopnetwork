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
class Attr extends Common
{
	public function attr()
	{
		return $this->fetch('attr/attr');
	}
}