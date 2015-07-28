<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\UserModel as UserModel;

class UserController extends Controller {

	public function _initialize(){
		$this->user = D('User');
	}

	## 获取最新的用户总览信息
	public function totalSummary()
	{
		$result = $this->user->getLatest();
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1, 'data'=>array()));
	}

	## 累计用户 新增用户 登录用户 活跃用户展示
	public function cumulationData()
	{
		## 展示的时间类型
		## 1： 天
		## 2： 周
		## 3： 月r
		$type = I('type', 1, 'intval');
		$result = $this->user->getLatestCumu($type); 
		var_dump($result); exit;
		if(!empty($result))
		{
			$this->assign("data", $result);
			# Visual/View/User/cumulateUser.htm
			$this->display("cumulateUser");
		}
		else
		{
			header("Content-Type: text/html;charset=utf-8");
			exit("信息获取失败");
		}
	}

	
}
