<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\UserModel as UserModel;

class UserController extends Controller {

	public function _initialize(){
		$this->user = D('User');
	}

	## 累计用户数展示
	public function cumulationData()
	{
		## 展示的时间类型
		## 1： 天
		## 2： 周
		## 3： 月
		$type = I('type', 1, 'intval');
		$result = $this->user->getLatestCumu($type); 
		if(!empty($result))
		{
			header("Content-Type: text/html;charset=utf-8");
			exit("信息获取失败");
		}
		else
		{
			$this->assign("data", $result);
			$this->display("cumulateUser");
		}
	}

	## 登录用户 活跃用户展示
	public function activeData()
	{

	}

	## 留存率展示
	public function retainData()
	{
		
	}
}
