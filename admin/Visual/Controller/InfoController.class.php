<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
	}

	## 前一天（最新）资讯top相关信息
	public function infoSummaryTop()
	{
		$topInfo = $this->info->topSummary();
		var_dump($topInfo); exit;
		if(!empty($topInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$topInfo));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 前一天（最新）资讯detail相关信息
	public function infoSummaryDetail()
	{
		$detailInfo = $this->info->detailSummary();
		var_dump($detailInfo); exit;
		if(!empty($detailInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$detailInfo));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 累计的资讯统计top信息展示
	public function infoAccumulateTop()
	{
		$re = $this->info->accumulateResultTop();
		var_dump($re); exit;
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

	public function infoAccumulateDetail()
	{
		$result = $this->info->accumulateResultDetail();
		var_dump($result); exit;
		if(!empty($result))
			return $this->ajaxReturn(array('code'=>1, 'data'=>$result));
		else
			return $this->ajaxReturn(array('code'=>-1));
	}

	## 获取每篇文章最新的累计统计指标值
	## 定时跑脚本的接口
	public function infoAccumulate()
	{
		$info = $this->info->accumulateInfo();
		$this->writeLog($info, 'infoAccumulate');
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
	
}
