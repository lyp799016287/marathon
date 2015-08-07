<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\UserModel as UserModel;

class UserController extends Controller {

	public function _initialize(){
		$this->user = D('User');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 8);
	}

	public function show(){
		$this->display("statUser");
	}

	## 获取最新的用户总览信息
	public function totalSummary()
	{
		$result = $this->user->getLatest();
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 累计用户展示
	public function totalDetail()
	{
		$type = I('type', 1, 'intval');
		$idx = 1;
		$result = $this->user->getLatestCumu($idx, $type); 
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 累计用户 新增用户 登录用户 活跃用户展示
	public function cumulationData()
	{
		## 展示的时间类型
		## 1： 天
		## 2： 周
		## 3： 月
		$type = I('type', 1, 'intval');
		$idx = 2;
		$result = $this->user->getLatestCumu($idx, $type); 
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
		// if(!empty($result))
		// {
		// 	$this->assign("data", $result);
		// 	# Visual/View/User/cumulateUser.htm
		// 	$this->display("cumulateUser");
		// }
		// else
		// {
		// 	header("Content-Type: text/html;charset=utf-8");
		// 	exit("信息获取失败");
		// }
	}

	
}
