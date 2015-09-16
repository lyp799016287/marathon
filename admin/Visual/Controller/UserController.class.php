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

	// public function show(){
	// 	$this->display("statUser");
	// }
	## 实时数据
	public function show()
	{
		$this->display('totalSummary');
	}
	## 基本指标
	public function basicInfo()
	{
		$this->display('basicInfo');
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

	## 准实时数据 表格中的内容
	## hour_stamp new_user active_user open_times
	public function detailByHour()
	{
		## 分页及排序顺序
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 30, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$result = $this->user->getHourly($data);
		if(!empty($result))
		{
			$result_data = $result['data'];
			## 计算总数
			$total_new_user = 0;
			$total_active_user = 0;
			$total_open_times = 0;
			for($i = 0; $i < count($result_data); $i++)
			{
				$total_new_user += $result_data[$i]['new_user'];
				$total_active_user += $result_data[$i]['active_user'];
				$total_open_times += $result_data[$i]['open_times'];
			}
			$len = count($result_data);
			$result_data[$len]['hour_stamp'] = "总计";
			$result_data[$len]['new_user'] = $total_new_user;
			$result_data[$len]['active_user'] = $total_active_user;
			$result_data[$len]['open_times'] = $total_open_times;
			// var_dump($result_data);exit;
			$this->ajaxReturn(array('code'=>1, 'data'=>$result_data, 'total'=>$result['total']));

		}
			
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 准实时数据 关键指标数据
	public function keyData()
	{
		$result = $this->user->getKeyData();
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 按天统计 每天用户的基础数据 折线图
	public function userByDay()
	{
		$type = I('type', 0, 'intval');
		$bgn_date = I('bgn_date', '');
		$end_date = I('end_date', '');
		if($bgn_date != '')
			$bgn_date = date("Y-m-d", $bgn_date);
		if($end_date != '')
			$end_date = date("Y-m-d", $end_date);
		$result = $this->user->userInfoByDay($type, $bgn_date, $end_date);
		## 将result的顺序倒置 按照时间从小到大
		if($result === false)
			$this->ajaxReturn(array('code'=>-1));
		$return_data = array();
		for($i = count($result) - 1; $i >= 0; $i--)
			$return_data[] = $result[$i];
		// var_dump($return_data); exit;
		## 当展示的时间段较长时 对datestamp字段进行缩减
		##（保证只显示5个 保证开始和结束的日期显示出来）
		// var_dump(count($return_data));
		if(count($return_data) > 7)
		{
			// var_dump("into if");
			$len = count($return_data);
			$tmp_len = $len - 2;
			$gap_len = $tmp_len / 3; ## 需要分段的各段长度
			for($i = 1; $i < $len - 1; $i++)
			{
				if($i % $gap_len != 0)
					$return_data[$i]['datestamp'] = '  ';
			}
		}
		$this->ajaxReturn(array('code'=>1, 'data'=>$return_data));

		## 
	}

	## 按天统计 每天用户的基础数据 Table
	public function userByDayTable()
	{
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 30, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$type = I('type', 0, 'intval');
		$bgn_date = I('bgn_date', '');
		$end_date = I('end_date', '');
		## 格式化时间
		if($bgn_date != '')
			$bgn_date = date("Y-m-d", $bgn_date);
		if($end_date != '')
			$end_date = date("Y-m-d", $end_date);

		$result = $this->user->getDailyBasic($data, $type, $bgn_date, $end_date);
		// var_dump($result);exit;
		if(!empty($result))
			$this->ajaxReturn(array('code'=>1, 'data'=>$result['data'], 'total'=>$result['total']));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

}
