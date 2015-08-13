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
		$result = $this->calPercentage($result);
		$result = $this->addV($result);
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
		$result = $this->calPercentage($result);
		$result = $this->addV($result);
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
		$result = $this->addV($result);
		// var_dump($result);
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

	## 用户留存数
	## 参数type用于区分 查看最近7天 最近30天的数据
	public function userRetain()
	{
		$type = I('type', 1, 'intval'); ## 默认查看最近7天
		$result = $this->desc->retainData($type);
		// var_dump($result);
		if(empty($result))
			$this->ajaxReturn(array('code'=>-1));
		$result = $this->calRetain($result);
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

	private function addV($ary)
	{
		for($i = 0; $i < count($ary); $i++)
			$ary[$i]['field_name'] = 'V ' . $ary[$i]['field_name'];
		return $ary;
	}

	## 计算用户的留存率
	## 返回值 array(array('data'=>, 'retain_3'=>, 'retain_7'=>, 'retain_30'=>))
	private function calRetain($result)
	{
		$result = array_slice($result, 1);
		// var_dump($result);
		$return_ary = array();
		for($i = 0; $i < count($result); $i++)
		{
			$return_ary[$i]['date'] = $result[$i]['register_date'];
			// $return_ary[$i]['new_user'] = $result[$i]['new_user'];
			if(empty($return_ary[$i]['new_user']))
			{
				// $return_ary[$i]['retain_2'] = 0;
				$return_ary[$i]['retain_3'] = 0;
				$return_ary[$i]['retain_7'] = 0;
				$return_ary[$i]['retain_30'] = 0;
			}
			else
			{
				// $result[$i]['retain_2'] = round(floatval($result[$i]['retain_2']) / $result[$i]['new_user'] * 100, 1);
				$return_ary[$i]['retain_3'] = round(floatval($result[$i]['retain_3']) / $result[$i]['new_user'] * 100, 1);
				$return_ary[$i]['retain_7'] = round(floatval($result[$i]['retain_7']) / $result[$i]['new_user'] * 100, 1);
				$return_ary[$i]['retain_30'] = round(floatval($result[$i]['retain_30']) / $result[$i]['new_user'] * 100, 1);
			}
			
		}
		return $return_ary;
	}

}
