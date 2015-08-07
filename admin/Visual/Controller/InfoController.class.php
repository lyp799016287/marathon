<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 7);
	}
	
	public function show(){
		$this->display("statInfo");
	}

	public function infoTop()
	{
		$type = I('type', 1, 'intval');
		if($type == 1)
			return $this->infoSummaryTop();
		elseif($type == 2)
			return $this->infoAccumulateTop();
	}

	## 前一天（最新）资讯top相关信息
	private function infoSummaryTop()
	{
		$topInfo = $this->info->topSummary();
		// var_dump($topInfo); exit;
		if(!empty($topInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$topInfo));
		else
			$this->ajaxReturn(array('code'=>-1));
	}

	## 累计的资讯统计top信息展示
	private function infoAccumulateTop()
	{
		$re = $this->info->accumulateResultTop();
		// var_dump($re); exit;
		if(!empty($re))
			$this->ajaxReturn(array('code'=>1, 'data'=>$re));
		else
			return $this->ajaxReturn(array('code'=>-1));
		// if(!empty($re))
		// {
		// 	$this->assign("top", $re['top']);
		// 	$this->assign("detail", $re['detail']);
		// 	# Visual/View/Info/infoTop.htm
		// 	$this->display("infoTop");
		// }
		// else
		// {
		// 	header("Content-Type: text/html;charset=utf-8");
		// 	exit("信息获取失败");
		// }
	}

	public function infoDetail()
	{
		$type = I('type', 1, 'intval');

		/************分页及排序信息***********/
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 20, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');


		if($type == 1)
			return $this->infoSummaryDetail($data);
		elseif($type == 2)
			return $this->infoAccumulateDetail($data);
	}

	## 前一天（最新）资讯detail相关信息
	private function infoSummaryDetail($data)
	{
		$rs = $this->info->detailSummaryTotal();
		if(!$rs){
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
		}

		$detailInfo = $this->info->detailSummary($data);
		// var_dump($detailInfo); exit;
		if(!empty($detailInfo))
			$this->ajaxReturn(array('code'=>1, 'data'=>$detailInfo, 'Total'=>$rs['total']));
		else
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
	}

	## 累计 资讯detail相关信息
	private function infoAccumulateDetail($data)
	{
		$rs = $this->info->accumulateRsDetailTotal();
		
		if(!$rs){
			$this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
		}
			
		$result = $this->info->accumulateResultDetail($data);
		// var_dump($result); exit;
		if(!empty($result))
			return $this->ajaxReturn(array('code'=>1, 'data'=>$result, 'Total'=>$rs['total']));
		else
			return $this->ajaxReturn(array('code'=>-1, 'data'=>array(), 'Total'=>0));
	}

	## 获取每篇文章最新的累计统计指标值
	## 定时跑脚本的接口
	public function infoAccumulate()
	{
		$info = $this->info->accumulateInfo();
		$this->writeLog($info, '\Visual\Info\infoAccumulate');
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
		// var_dump($dir_name);
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
