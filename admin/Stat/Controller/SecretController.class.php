<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\SecretModel as SecretModel;

class SecretController extends Controller {

	public function _initialize()
	{
		$this->secret = D('Secret');
	}

	public function calSecretDaily()
	{
		$re = $this->secret->secretDaily();
		$this->writeLog($re, "Stat/Secret/calSecretDaily");
	}

	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ' . $result['code'] . "  " . $result['message'];
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
