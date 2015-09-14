<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\UserModel as UserModel;

class UserController extends Controller {

	public function _initialize()
	{
		$this->user = D('User');
	}

	## 计算用户数的基础信息
	public function userSummary()
	{
		$re = $this->user->calSummary();
		$this->writeLog($re, 'Stat/User/userSummary');
	}

	## 准实时计算用户数
	public function userSummaryRealtime()
	{
		$re = $this->user->calRealtime();
		$this->writeLog($re, 'Stat/User/userSummaryRealtime');
	}

	## 计算用户留存率
	public function userRetain()
	{
		$re = $this->user->calRetain();
		$this->writeLog($re, 'Stat/User/userRetain');
	}

	## 用户使用（打开）APP的频率
	public function userFreq()
	{
		$re = $this->user->calFreq();
		$this->writeLog($re, 'Stat/User/userFreq');
	}

	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ' . $result['code'] . '  ' . $result['message'];
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
