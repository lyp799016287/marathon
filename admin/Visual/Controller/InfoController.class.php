<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
	}

	## 资讯的相关信息
	public function infoSummary()
	{
		$topInfo = $this->info->topSummary();
		$detailInfo = $this->info->detailSummary();
		var_dump($topInfo); var_dump($detailInfo); exit;

		if(!empty($topInfo) && !empty($detailInfo))
		{
			$this->assign("top", $topInfo);
			$this->assign("detail", $detailInfo);
			# Visual/View/Info/infoTop.htm
			$this->display("infoTop");
		}
		else
		{
			header("Content-Type: text/html;charset=utf-8");
			exit("信息获取失败");
		}
	}

	
}
