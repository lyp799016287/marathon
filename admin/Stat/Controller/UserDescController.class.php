<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\DescModel as DescModel;

class UserDescController extends Controller {

	public function _initialize()
	{
		$this->desc = D('Desc');
	}

	## 用户设备信息（可实时）
	public function deviceSummary()
	{
		$re = $this->desc->calDevice();
		$this->writeLog($re, 'Stat/UserDesc/deviceSummary');
	}
	
	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ' . $result['code'] . '   ' . $result['message'];
		$log_str .= "\n";
		$dir_name = dirname(dirname(dirname(__FILE__)));
		$dir_name = $dir_name . "/Runtime/ScriptLogs/";
		// var_dump($dir_name);
		$date = date('Y-m-d', time());
		$file_name = $dir_name . $date . ".txt";
		// var_dump($file_name);
		// var_dump($log_str);
		try
		{
			$f_obj = fopen($file_name, 'a');
			$f_result = fwrite($f_obj, $log_str);
			fclose($f_obj);
		}
		catch(Exception $e)
		{
			var_dump("write log error");
			print $e->getMessage();
			exit();
		}
	}

}
