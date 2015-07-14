<?php
namespace Import\Controller;
require THINK_LIB_PATH . 'Library/Org/Util/PHPExcel.class.php';
require THINK_LIB_PATH . 'Library/Org/Util/PHPExcel/IOFactory.php'; ## 引入IOFactory类 检查该路径是否正确
require THINK_LIB_PATH . 'Library/Org/Util/PHPExcel/Reader/Excel5.php';

use Think\Controller;
use Import\Model\ImportModel as ImportModel;

## 将excel中的信息导入到数据库中
class ImportController extends Controller{

	## 检查文件是否存在
	private function isExist($fileName)
	{
		if(file_exists($fileName))
			return true;
		else
			return false;
	}

	## 获取文件的访问路径和姓名 
	## 将该目录下的所有文件合并
	private function getData()
	{
		$rootPath = "/yzserver/python/result/News/";
		## 获取当前日期
		$name = date('Ymd', time());
		$path = $rootPath . $name . "/" . $name . "_all.xls";
		// var_dump($path);
		return $path;

	}

	public function readExcel()
	{
		$fileName = $this->getData();
		// var_dump($fileName);
		if($this->isExist($fileName))
		{
			$excel = new \PHPExcel_IOFactory();
			$objReader = $excel->createReader('Excel5'); ## 只能读取excel997-2003版本
			//$objReader = PHPExcel_IOFactory::createReader('Excel5'); ## 只能读取excel997-2003版本
			$objPHPExcel = $objReader->load($fileName);
			$sheet = $objPHPExcel->getSheet(0); ## 读取第一个sheet
	
			$rowCnt = $sheet->getHighestRow(); ## 读取总行数
			$colCnt = $sheet->getHighestColumn(); ## 读取总列数
			
			$colLen = ord($colCnt) - ord('A') + 1;
			## 拼接SQL语句
			$allValues = 'INSERT INTO t_info_raw(source, title, url, pub_date, summary, author, content, imgTag) VALUES';
			for($i = 1; $i <= $rowCnt; $i++) ## 从1开始
			{
				$data = array();
				for($j = 'A'; $j <= $colCnt; $j++) ## 从A开始
					$data[] = $sheet->getCell($j.$i)->getValue();
				// var_dump($data);exit;
				// var_dump($i);
				// var_dump("********************************Begin***********************************\n");
				// var_dump($this->intoSqlStr($data, $colLen));
				// var_dump("*********************************End************************************\n");
				if($i != $rowCnt)
					$allValues .= $this->intoSqlStr($data, $colLen) . ', ';
				else
					$allValues .= $this->intoSqlStr($data, $colLen);
			}
			// var_dump($allValues);
			## model层执行语句
			$import = new ImportModel();
			$re = $import->intoTable($allValues);
			if($re !== false)
				$this->ajaxReturn(array('code'=>1, 'message'=>'数据导入到数据库成功:' . date('Y-m-d H:i:s', time())), 'JSON');
			else
				$this->ajaxReturn(array('code'=>-1, 'message'=>'数据导入失败'), 'JSON');
		}
		else
		{
			var_dump("Cannot find file " . $fileName);
			$this->ajaxReturn(array('code'=>-2, 'message'=>'需要读取的excel文件不存在'), 'JSON');
		}
	}

	## 拼接sql语句
	private function intoSqlStr($ary, $col)
	{
		$finalStr = '';
		for($i = 0; $i < $col; $i++)
			if($i == 0)
				$finalStr .= "('" . $ary[$i] . "', ";
			elseif($i == $col - 1)
				$finalStr .= "'" . $ary[$i] . "')";
			else
				$finalStr .= "'" . $ary[$i] . "', ";
		return $finalStr;
	}

}
