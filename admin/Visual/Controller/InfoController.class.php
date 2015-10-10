<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 7);
	}
	
	public function show(){
		$this->display("statInfo");
	}

	public function infoLabel(){
		$this->display("infoLabel");
	}

	public function infoPub()
	{
		$this->display("infoPub");
	}

	public function infoTop()
	{
		$type = I('type', 1, 'intval');
		if($type == 1)
			return $this->infoSummaryTop();
		elseif($type == 2)
			return $this->infoAccumulateTop();
	}

	## 前一天（最新）资讯top相关信息
	private function infoSummaryTop()
	{
		$topInfo = $this->info->topSummary();
		// var_dump($topInfo); exit;
		if(!empty($topInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$topInfo));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 累计的资讯统计top信息展示
	private function infoAccumulateTop()
	{
		$re = $this->info->accumulateResultTop();
		// var_dump($re); exit;
		if(!empty($re))
			$this->ajaxReturn(array('code'=>1, 'data'=>$re));
		else
			return $this->ajaxReturn(array('code'=>-1));
		// if(!empty($re))
		// {
		// 	$this->assign("top", $re['top']);
		// 	$this->assign("detail", $re['detail']);
		// 	# Visual/View/Info/infoTop.htm
		// 	$this->display("infoTop");
		// }
		// else
		// {
		// 	header("Content-Type: text/html;charset=utf-8");
		// 	exit("信息获取失败");
		// }
	}

	public function infoDetail()
	{
		$type = I('type', 1, 'intval');

		/************分页及排序信息***********/
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 20, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');


		if($type == 1)
			return $this->infoSummaryDetail($data);
		elseif($type == 2)
			return $this->infoAccumulateDetail($data);
	}

	## 前一天（最新）资讯detail相关信息
	private function infoSummaryDetail($data)
	{
		$rs = $this->info->detailSummaryTotal();
		if(!$rs){
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
		}

		$detailInfo = $this->info->detailSummary($data);
		// var_dump($detailInfo); exit;
		if(!empty($detailInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$detailInfo, 'Total'=>$rs['total']));
		else
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
	}

	## 累计 资讯detail相关信息
	private function infoAccumulateDetail($data)
	{
		$rs = $this->info->accumulateRsDetailTotal();
		
		if(!$rs){
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
		}
			
		$result = $this->info->accumulateResultDetail($data);
		// var_dump($result); exit;
		if(!empty($result))
			return $this->ajaxReturn(array('code'=>1, 'data'=>$result, 'Total'=>$rs['total']));
		else
			return $this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
	}

	## 获取每篇文章最新的累计统计指标值
	## 定时跑脚本的接口
	public function infoAccumulate()
	{
		$info = $this->info->accumulateInfo();
		$this->writeLog($info, '\Visual\Info\infoAccumulate');
	}

	## 跑脚本的内容  写log
	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ' . $result['code'] . "   " . $result['message'];
		$log_str .= "\n";
		$dir_name = dirname(dirname(dirname(__FILE__)));
		$dir_name = $dir_name . "/Runtime/ScriptLogs/";
		// var_dump($dir_name);
		$date = date('Y-m-d', time());
		$file_name = $dir_name . $date . ".txt";
		try
		{
			$f_obj = fopen($file_name, 'a');
			$f_result = fwrite($f_obj, $log_str);
			fclose($f_obj);
		}
		catch(Exception $e)
		{
			print $e->getMessage();
			exit();
		}
	}

	## added by Bella 2015-09-30
	## 资讯标签统计 折线图
	public function infoTypeGraph()
	{
		$date_bgn = I('bgn_date', '');
		$date_end = I('end_date', '');
		if(empty($date_bgn) || empty($date_end))
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
		$result = $this->info->getInfoType($date_bgn, $date_end);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
		{
			// var_dump($result); exit;
			$data = $result['data'];
			$data_len = count($data);
			$type = $result['type']; ## 版本的最大集
			$type_name = $result['type_name'];
			$datestamp = array(); ## 日期的最大集
			while($date_bgn <= $date_end)
			{
				$datestamp[] = $date_bgn;
				$date_bgn = date('Y-m-d', strtotime("+1 day", strtotime($date_bgn)));
			}
			for($i = 0; $i < count($datestamp); $i++)
				for($j = 0; $j < count($type); $j++)
				{
					$tmp_date = $datestamp[$i];
					$tmp_type = $type[$j]['category'];
					$flag = 0;
					for($k = 0; $k < count($data); $k++)
						if($data[$k]['pub_date'] == $tmp_date && $data[$k]['category'] == $tmp_type)
						{
							$flag = 1;
							break;
						}
					if($flag == 0) ## 需要增加的记录
					{
						$data[$data_len]['pub_date'] = $tmp_date;
						$data[$data_len]['category'] = $tmp_type;
						$data[$data_len]['cnt'] = 0;
						$data_len++;
					}
				}
			$result_ary = array();
			for($i = 0; $i < count($datestamp); $i++)
			{
				$tmp_len = 0;
				$result_ary[$i]['datestamp'] = $datestamp[$i];
				for($j = 0; $j < count($data); $j++)
					if($data[$j]['pub_date'] == $result_ary[$i]['datestamp'])
					{
						$result_ary[$i]['data'][$tmp_len]['category'] = $data[$j]['category'];
						$result_ary[$i]['data'][$tmp_len]['cnt'] = $data[$j]['cnt'];
						$tmp_len++;
					}
			}
			$this->ajaxReturn(array('code'=>1, 'data'=>$result_ary, 'type'=>$type, 'name'=>$type_name));
		}	
	}

	## 资讯标签统计 table
	## added by Bella 2015-10-09
	public function infoTypeTable()
	{
		$date_bgn = I('bgn_date', '');
		$date_end = I('end_date', '');
		if(empty($date_bgn) || empty($date_end))
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));

		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 30, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$result = $this->info->infoTable($date_bgn, $date_end, $data);
		// var_dump($result);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result['data'], 'total'=>$result['len'][0]['cnt']));
	}

	## added by Bella 2015-10-10
	## 资讯操作 table数据
	public function infoOperateTable()
	{
		$date_bgn = I('bgn_date', '');
		$date_end = I('end_date', '');
		if(empty($date_bgn) || empty($date_end))
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));

		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 30, 'intval');
		// $data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$result = $this->info->getOperateDetail($date_bgn, $date_end);// 返回按照日期降序排列的数据
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
		{
			$len = count($result);
			$start = ($data['current_page'] - 1) * $data['page_size'];
        	$len = $data['page_size'] - 1;
        	if(isset($data['sort_name']) && !empty($data['sort_name']))
        		$result = array_reverse($result);
        	$result = array_slice($result, $start, $len);
        	
			$this->ajaxReturn(array('code'=>1, 'data'=>$result, 'total'=>$len));
		}
			
	}


	## added by Bella 2015-10-10
	## 资讯操作 折线图数据
	public function infoOperateGraph()
	{
		$date_bgn = I('bgn_date', '');
		$date_end = I('end_date', '');
		if(empty($date_bgn) || empty($date_end))
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'));
		$type = I('type', 1, 'intval');
		switch($type)
		{
			case 1:
				$result = $this->info->infoPub($date_bgn, $date_end);
				break;
			case 2:
				$result = $this->info->infoComment($date_bgn, $date_end);
				break;
			case 3:
				$result = $this->info->infoFav($date_bgn, $date_end);
				break;
			case 4:
				$result = $this->info->infoShare($date_bgn, $date_end);
				break;
			case 5:
				$result = $this->info->secretPub($date_bgn, $date_end);
				break;
			case 6:
				$result = $this->info->secretComment($date_bgn, $date_end);
				break;
			default:
				$result = $this->info->infoPub($date_bgn, $date_end);
				break;
		}
		// $result = $this->info->getOperateSummary($date_bgn, $date_end);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行错误'));
		else
		{
			$datestamp = array(); ## 时间的最大集
			while($date_bgn <= $date_end)
			{
				$datestamp[] = $date_end;
				$date_end = date('Y-m-d', strtotime("-1 day", strtotime($date_end)));
			}
			$result_len = count($result);
			for($i = 0; $i < count($datestamp); $i++)
			{	
				$flag = 0;
				for($j = 0; $j < count($result); $j++)
					if($result[$j]['datestamp'] == $datestamp[$i])
					{
						$flag = 1;
						break;
					}
				if($flag == 0)
				{
					$result[$result_len]['datestamp'] = $datestamp[$i];
					$result[$result_len]['cnt'] = 0;
					$result_len++;
				}
			}
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
		}
			
	}
	
}
