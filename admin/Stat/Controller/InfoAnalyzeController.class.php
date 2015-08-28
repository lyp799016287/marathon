<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\InfoAnalyzeModel as InfoAnalyzeModel;

class InfoAnalyzeController extends Controller {

	public function _initialize()
	{
		$this->ana = D('InfoAnalyze');
	}

	## 拆分表t_info_daily/t_info_accumulate表中的keys字段
	## 定时任务调用的接口
	public function keySplit()
	{
		$result = $this->ana->intoWords();
		$this->writeLog($result, 'Stat/InfoAnalyze/keySplit');
	}


	public function splitKeywords()
	{
		$key = I('keywords', '');
		$result = $this->ana->getWords($key);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'执行失败'));
		else
			$this->ajaxReturn(array('code'=>1, 'message'=>'执行成功'));
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
