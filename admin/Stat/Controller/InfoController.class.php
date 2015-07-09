<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize()
	{
		$this->info = D('info');
	}

	## 计算每天的浏览量
	public function scanDaily()
	{
		$result = $this->info->calScanDaily();
		$this->writeLog($result, 'scanDaily');
	}

	## 计算每天的评论量
	public function commentDaily()
	{
		$result = $this->info->calCommentDaily();
		$this->writeLog($result, 'commentDaily');
	}

	## 计算每天的分享量
	public function shareDaily()
	{
		$result = $this->info->calShareDaily();
		$this->writeLog($result, 'shareDaily');
	}

	## 将每天的资讯浏览量 评论量 分享量集合
	public function mergeInfoDaily()
	{
		$result = $this->info->mergeInfo();
		$this->writeLog($result, 'mergeInfoDaily');
	}

	## 跑脚本的内容  写log
	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ';
		if($result !== false)
		{
			for($i = 0; $i < count($result); $i++)
				$log_str .= $result[$i] . ", ";
		}
		else
			$log_str .= 'execute error';
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

	## 计算文章的综合评分
	public function calGeneralScore()
	{
		
	}


	
}
