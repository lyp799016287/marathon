<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\DescModel as DescModel;

class UserDescController extends Controller {

	public function _initialize(){
		$this->desc = D('Desc');
	}

	public function show()
	{
		$this->display('statUserDesc');
	}

	## 用户设备信息相关统计量
	## 获取的数据 截止昨天24:00

	## 计算用户sdk分布
	public function userSdk()
	{
		$type = 1;
		$result = $this->desc->deviceData($type);
		// var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 计算用户手机系统版本分布
	public function userSysVersion()
	{
		$type = 2;
		$result = $this->desc->deviceData($type);
		// var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户使用的APP版本分布
	public function userAppVersion()
	{
		$type = 3;
		$result = $this->desc->deviceData($type);
		$result = $this->calPercentage($result);
		var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户设备型号分布
	public function userDevice()
	{
		$result = $this->desc->modelData();
		// var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户留存数（最近7天内）
	public function userRetain()
	{
		$result = $this->desc->retainData();
		// var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户使用APP的时间分布
	public function userTime()
	{
		$result = $this->desc->timeData();
		// var_dump($result);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户做分享的分布情况
	public function userShare()
	{
		$result = $this->desc->shareData();
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 计算各个部分对应的百分比
	private function calPercentage($ary)
	{
		$total_num = 0;
		for($i = 0; $i < count($ary); $i++)
			$total_num += intval($ary[$i]['part_num']);
		if($total_num == 0)
			return false;

		for($i = 0; $i < count($ary); $i++)
		{
			## 获得百分数, 保留小数点后一位
			$ary[$i]['part_num'] = round(floatval($ary[$i]['part_num']) / $total_num * 100, 1); 
		}

		return $ary;
	}

}
