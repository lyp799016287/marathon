<?php
namespace Stat\Controller;
use Think\Controller;
use Stat\Model\PortModel as PortModel;

class PortController extends Controller {

	public function _initialize()
	{
		$this->port = D('Port');
	}

	## 计算每天接口报错的数量
	public function errorDaily()
	{
		$result = $this->port->calDaily();
		$this->writeLog($result, "Stat\Port\errorDaily");
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
