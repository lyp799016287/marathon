<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\UserModel as UserModel;

class UserController extends Controller {

	public function _initialize()
	{
		$this->user = D('user');
	}

	public function userSummary()
	{
		$this->user->calSummary();
	}



	

	
}
