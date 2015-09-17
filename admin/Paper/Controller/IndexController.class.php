<?php
namespace Paper\Controller;
use Think\Controller;
class IndexController extends Controller {
	
	public function _initialize(){
		checkPrivilege();
	}

	public function getList(){
		echo "This is getList interface";
	}

	public function show(){
		var_dump($_SESSION);
	}
}