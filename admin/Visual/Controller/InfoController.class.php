<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
	}

	## 资讯的相关信息
	public function infoSummary()
	{
		$topInfo = $this->info->topSummary();
		$detailInfo = $this->info->detailSummary();
		var_dump($topInfo); var_dump($detailInfo); exit;

		if(!empty($topInfo) && !empty($detailInfo))
		{
			$this->assign("top", $topInfo);
			$this->assign("detail", $detailInfo);
			# Visual/View/Info/infoTop.htm
			$this->display("infoTop");
		}
		else
		{
			header("Content-Type: text/html;charset=utf-8");
			exit("信息获取失败");
		}
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

	## 累计的资讯统计信息展示
	public function showAccumulate()
	{
		$re = $this->info->accumulateResult();
		var_dump($re); exit;

		if(!empty($re))
		{
			$this->assign("top", $re['top']);
			$this->assign("detail", $re['detail']);
			# Visual/View/Info/infoTop.htm
			$this->display("infoTop");
		}
		else
		{
			header("Content-Type: text/html;charset=utf-8");
			exit("信息获取失败");
		}
	}

	
}
