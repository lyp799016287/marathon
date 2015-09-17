<?php
namespace Paper\Controller;
use Think\Controller;
class IndexController extends Controller {
	
	public function _initialize(){
		//checkPrivilege();
	}

	public function getList(){
		echo "This is getList interface";
	}

	public function test(){
		$user_id = 4;
		$rbac = new \Org\Util\Rbac;	//导入权限验证类
		var_dump($rbac);
		$rbac->saveAccessList($user_id);
	}

	public function show(){
		var_dump($_SESSION);
	}
}