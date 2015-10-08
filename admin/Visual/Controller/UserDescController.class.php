<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\DescModel as DescModel;

class UserDescController extends Controller {

	public function _initialize(){
		$this->desc = D('Desc');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 10);
		$this->top = 5; ## 显示排名前五的  剩下的用“其它”代替

		$this->distri = D('Distri');
	}

	public function show()
	{
		$this->display('statUserDesc');
	}

	public function retainDefined()
	{
		$this->display('retainUser1');
	}

	## 用户设备信息相关统计量
	## 获取的数据 截止昨天24:00

	## 计算用户sdk分布
	public function userSdk()
	{
		$type = 1;
		$result = $this->desc->deviceData($type);
		$result = $this->calPercentage($result);
		// $result = $this->addV($result);
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
		// $result = $this->addV($result);
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
		// $result = $this->addV($result);
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
	## 排名前5的正常显示 剩下的部分全部归为其他
	private function calPercentage($ary)
	{
		$result_ary = array();
		$total_num = 0;
		for($i = 0; $i < count($ary); $i++)
			$total_num += intval($ary[$i]['part_num']);
		if($total_num == 0)
			return false;

		$other_num = 0; ##“其它”项的数值
		for($i = 0; $i < count($ary); $i++)
		{
			if($i < $this->top)
			{
				$result_ary[$i]['field_name'] = 'V ' . $ary[$i]['field_name'];
				## 获得百分数, 保留小数点后一位
				$result_ary[$i]['part_num'] = round(floatval($ary[$i]['part_num']) / $total_num * 100, 1); 
			}
			else
				$other_num += $ary[$i]['part_num'];	
		}
		if($other_num > 0) ## 存在“其它”项
		{
			$result_ary[$this->top]['field_name'] = '其它';
			$result_ary[$this->top]['part_num'] = round(floatval($other_num) / $total_num * 100, 1); 
		}
		return $result_ary;
	}

	// private function addV($ary)
	// {
	// 	for($i = 0; $i < count($ary); $i++)
	// 		$ary[$i]['field_name'] = 'V ' . $ary[$i]['field_name'];
	// 	return $ary;
	// }

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

	public function provinceDis()
	{
		$type = 1;
		$result = $this->distri->userDetail($type);
		if(empty($result))
			$this->ajaxReturn(array('code'=>-1, 'message'=>"执行错误"));
		// var_dump($result);
		$result = $this->calUserPer($result);
		$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	public function hospitalLevel()
	{
		$type = 2;
		$result = $this->distri->userDetail($type);
		if(empty($result))
			$this->ajaxReturn(array('code'=>-1, 'message'=>"执行错误"));
		$result = $this->calUserPer($result);
		$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	public function departDis()
	{
		$type = 3;
		$result = $this->distri->userDetail($type);
		if(empty($result))
			$this->ajaxReturn(array('code'=>-1, 'message'=>"执行错误"));
		$result = $this->calUserPer($result);
		$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	public function titleDis()
	{
		$type = 4;
		$result = $this->distri->userDetail($type);
		if(empty($result))
			$this->ajaxReturn(array('code'=>-1, 'message'=>"执行错误"));
		//职称中如果有“其他” 将其改成“其他职称”
		for($i = 0; $i < count($result); $i++)
			if(trim($result[$i]['field_name']) == "其他")
			{
				$result[$i]['field_name'] = "其他职称";
				break;
			}	
		$result = $this->calUserPer($result);
		$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## 新用户自定义留存 趋势图
	## 留存用户 新用户 
	## added by Bella 2015-10-08
	public function userRetainSelfdefined()
	{
		$bgn_date = I('bgn_date', '');
        $end_date = I('end_date', '');
        if(empty($bgn_date) || empty($end_date))
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
        $result = $this->desc->retainSummary($bgn_date, $end_date);
        if($result === false)
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'查询失败'));
        else
        	$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}


	## 新用户自定义留存 趋势图
	## 留存率 
	## added by Bella 2015-10-08
	public function userRetainRate()
	{
		$bgn_date = I('bgn_date', '');
        $end_date = I('end_date', '');
        if(empty($bgn_date) || empty($end_date))
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
        $result = $this->desc->retainSummary($bgn_date, $end_date);
        if($result === false)
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'查询失败'));
        else
        {
        	//计算留存率
        	$result_rate = array();
        	for($i = 0; $i < count($result); $i++)
        	{
        		$result_rate[$i]['datestamp'] = $result[$i]['datestamp'];
        		if(empty($result[$i]['new_user']) || empty($result[$i]['retain_user']))
        			$result_rate[$i]['retain_rate'] = 0;
        		else
        			$result_rate[$i]['retain_rate'] = round((float)$result[$i]['retain_user'] / $result[$i]['new_user'] * 100, 2);
        	}
        		
        	// var_dump($result_rate); exit;
        	$this->ajaxReturn(array('code'=>1, 'data'=>$result_rate));
        }
	}

	## 新用户自定义留存 table
	## added by Bella 2015-10-08
	public function userRetainTable()
	{
		$bgn_date = I('bgn_date', '');
        $end_date = I('end_date', '');
        if(empty($bgn_date) || empty($end_date))
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
		## 分页及排序顺序
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 30, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$result = $this->desc->retainTable($data, $bgn_date, $end_date);
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result['data'], 'total'=>$result['len']));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 用户留存率  more detail
	## added by Bella 2015-09-23
	public function userRetainMore()
	{
		$bgn_date = I('bgn_date', '');
        $end_date = I('end_date', '');
        if(empty($bgn_date) || empty($end_date))
        	$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
		$result = $this->desc->calNewRetain($bgn_date, $end_date);
		if($result === false)
			$this->ajaxReturn(array('code'=>-2, 'message'=>'查询失败'));
		// var_dump($result); exit;
		$re_ary = array();
		for($i = 0; $i < count($result); $i++)
		{
			if(empty($result[$i]['new_user']))
			{
				$re_ary[$i]['retain_1'] = 0;
				$re_ary[$i]['retain_2'] = 0;
				$re_ary[$i]['retain_3'] = 0;
				$re_ary[$i]['retain_4'] = 0;
				$re_ary[$i]['retain_5'] = 0;
				$re_ary[$i]['retain_6'] = 0;
				$re_ary[$i]['retain_7'] = 0;
				$re_ary[$i]['retain_14'] = 0;
				$re_ary[$i]['retain_30'] = 0;
				$re_ary[$i]['new_user'] = 0;
				$re_ary[$i]['date'] = $result[$i]['register_date'];
				continue;
			}
			$re_ary[$i]['date'] = $result[$i]['register_date'];
			$re_ary[$i]['new_user'] = $result[$i]['new_user'];
			$re_ary[$i]['retain_1'] = round(floatval($result[$i]['retain_1']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_2'] = round(floatval($result[$i]['retain_2']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_3'] = round(floatval($result[$i]['retain_3']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_4'] = round(floatval($result[$i]['retain_4']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_5'] = round(floatval($result[$i]['retain_5']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_6'] = round(floatval($result[$i]['retain_6']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_7'] = round(floatval($result[$i]['retain_7']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_14'] = round(floatval($result[$i]['retain_14']) / $result[$i]['new_user'] * 100, 2);
			$re_ary[$i]['retain_30'] = round(floatval($result[$i]['retain_30']) / $result[$i]['new_user'] * 100, 2);
		}
		$this->ajaxReturn(array('code'=>1, 'data'=>$re_ary));
	}

	## 计算用户分布的百分比
	private function calUserPer($ary)
	{
		$return_ary = array();
		$total_num = 0;
		for($i = 0; $i < count($ary); $i++)
			$total_num += intval($ary[$i]['num']);
		$other_num = 0;
		for($j = 0; $j < count($ary); $j++)
		{
			if($j < $this->top)
			{
				$return_ary[$j]['field_name'] = $ary[$j]['field_name'];
				$return_ary[$j]['num'] = $ary[$j]['num'];
			}
			else
			{
				$other_num += $ary[$j]['num'];
			}
		}
		if($other_num > 0)
		{
			$return_ary[$this->top]['field_name'] = "其他";
			$return_ary[$this->top]['num'] = $other_num;
		}
		return $return_ary;
	}

	## added by Bella 2015-09-28
	## 渠道分布的table数据
	public function channelTable()
	{
		$date = I('date', '');
		$date = empty($date) ? date('Y-m-d', strtotime("-1 day")) : $date; ## 默认显示前一天
		$result = $this->desc->getChannelByDate($date);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## added by Bella 2015-09-28
	## 渠道分布的折线图数据
	public function channelGraph()
	{
		$date_bgn = I('bgn', '');
		$date_end = I('end', '');
		$type = I('type', 1, 'intval');
		$result = $this->desc->getGraphData($date_bgn, $date_end, $type);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## added by Bella 2015-09-29
	## 终端设备的table数据
	public function sdkTable()
	{
		$date = I('date', '');
		$date = empty($date) ? date('Y-m-d', strtotime("-1 day")) : $date; ## 默认显示前一天
		$result = $this->desc->getSdkByDate($date);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## added by Bella 2015-09-30
	## 终端设备的折线图数据
	public function sdkGraph()
	{
		$date_bgn = I('bgn', '');
		$date_end = I('end', '');
		$type = I('type', 1, 'intval');
		$result = $this->desc->getSdkGraph($date_bgn, $date_end, $type);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## added by Bella 2015-09-29
	## 版本分布的table数据
	public function versionTable()
	{
		$date = I('date', '');
		$date = empty($date) ? date('Y-m-d', strtotime("-1 day")) : $date; ## 默认显示前一天
		$result = $this->desc->getVersionByDate($date);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## added by Bella 2015-09-29
	## 版本分布的折线数据
	public function versionGraph()
	{
		$date_bgn = I('bgn', '');
		$date_end = I('end', '');
		$type = I('type', 1, 'intval');
		$result = $this->desc->getVersionLine($date_bgn, $date_end, $type);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

}
