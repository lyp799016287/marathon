<?php
namespace Stat\Model;
use Think\Model;

class InfoAnalyzeModel extends Model {
	protected $connection = 'DB_STAT';
    protected $trueTableName = 't_info_daily';

    public function _initialize()
    {
        $this->file_name = 't_info_accumulate_time.txt';
    }

    public function intoWords()
    {
    	// 读取文件 获得当前最大的时间戳
    	$time_str = '';
    	try
    	{
    		$f_obj = fopen($this->file_name, 'r');
    		if($f_obj)
    		{
    			$time_str = fgets($f_obj);
    			$time_str = trim($time_str);
    		}
    		fclose($f_obj);
    	}
    	catch(Exception $e)
		{
			print $e->getMessage();
			// exit();
			return array('code'=>-4, 'message'=>'读取文件失败');
		}
    	
		// var_dump($time_str);
    	$where_clause = '';
    	if($time_str != '')
	    	$where_clause = " WHERE create_time > '" . $time_str . "'";
	    $sql = "SELECT info_id, `keys` FROM t_info_accumulate " . $where_clause;
	    $result = $this->query($sql);
	    // var_dump($result);
	    if($result === false)
	    	return array('code'=>-1, 'message'=>'查询错误');
	    $re = $this->splitWords($result);
	    if($re === true)
	    {
	    	//获取最大的时间戳 重新写入
	    	$sql = "SELECT MAX(create_time) create_time FROM t_info_accumulate " . $where_clause;
	    	var_dump($sql);
	    	$re_time = $this->query($sql);
	    	// var_dump($re_time);
	    	if($re_time === false)
	    		return array('code'=>-3, 'message'=>'获取时间戳失败');
	    	$time_str = $re_time[0]['create_time'];
	    	$re_file = $this->intoFile($time_str);
	    	if($re_file === false)
	    		return array('code'=>-5, 'message'=>'写入文件失败');
	    	return array('code'=>1, 'message'=>'执行成功');
	    }
	    else
	    	return array('code'=>-2, 'message'=>'插入数据错误');
    }

	private function splitWords($ary)
	{
		$error_str = "";
		for($i = 0; $i < count($ary); $i++)
		{
			$info_id = $ary[$i]['info_id'];
			$words = explode(',', $ary[$i]['keys']);
			for($j = 0; $j < count($words); $j++)
			{
				$word = $words[$j];
				$insertSql = <<<EOF
				INSERT INTO t_info_keys (info_id, key_word)
				VALUES({$info_id}, "{$word}")
EOF;
				$re = $this->execute($insertSql);
				if($re === false)
					$error_str .= $info_id . ": " . $word . ", ";
			}
		}
		if($error_str == '')
			return true;
		else
			return $error_str;
	}

	## 最大时间戳写入文件
	private function intoFile($str)
	{
		var_dump($str);
		try
		{
			$f_obj = fopen($this->file_name, 'w');
			fwrite($f_obj, $str);
			fclose($f_obj);
		}
		catch(Exception $e)
		{
			print $e->getMessage();
			// exit();
			return false;
		}
		return true;
	}


}