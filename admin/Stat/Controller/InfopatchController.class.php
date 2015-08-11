<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\InfoModel as InfoModel;

class InfopatchController extends Controller {

	public function _initialize()
	{
		$this->infopatch = D('Infopatch');
	}

	## 更新表t_info_daily中的title pub_time字段
	public function patchInfo()
	{
		$result = $this->infopatch->infoPatch();
		$this->writeLog($result, "Stat/Infopath/patchInfo");
	}

	## 跑脚本的内容  写log
	private function writeLog($result, $tag)
	{
		$log_str = "";
		$time = date('Y-m-d H:i:s', time());
		$log_str .= $time . ' ' . $tag . ': ' . $result['code'] . "   " . $result['message'];
		// if($result !== false)
		// {
		// 	for($i = 0; $i < count($result); $i++)
		// 		$log_str .= $result[$i] . ", ";
		// }
		// else
		// 	$log_str .= 'execute error';
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
