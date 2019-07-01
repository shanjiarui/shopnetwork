<?php
namespace app\admin\controller;
/**
 * 
 */
use think\Controller;
class Index extends Common
{
	public function index()
	{
		return $this->fetch('index/index');
	}
}