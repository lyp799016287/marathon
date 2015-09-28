<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\DescModel as DescModel;

class UserDescController extends Controller {

	public function _initialize()
	{
		$this->desc = D('Desc');
	}

	## 用户设备信息及来源渠道（可实时）
	public function deviceSummary()
	{
		$re = $this->desc->calDevice();
		$this->writeLog($re, 'Stat/UserDesc/deviceSummary');
	}

	## 每天新增用户的列表
	// public function newUserList()
	// {
	// 	$re = $this->desc->getNewUserInfo();
	// 	$this->writeLog($re, 'Stat/UserDesc/newUserList');
	// }

	## 按照渠道划分用户
	public function userByChannel()
	{
		$re = $this->desc->userChannel();
		$this->writeLog($re, 'Stat/UserDesc/userByChannel');
	}

	## 按照app版本划分用户
	public function userByVersion()
	{
		$re = $this->desc->userVersion();
		$this->writeLog($re, 'Stat/UserDesc/userByVersion');
	}

	## 按照手机系统版本划分用户
	public function userBySysVersion()
	{
		$re = $this->desc->userSysVersion();
		$this->writeLog($re, 'Stat/UserDesc/userBySysVersion');
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
