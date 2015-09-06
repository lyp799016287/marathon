<?php
namespace Shell\Controller;
use Think\Controller;

class DataController extends Controller {
	
	public function addNewUsers(){
		
		$com_users = C('COMMENT_USERS');

		var_dump($com_users);
	}
}