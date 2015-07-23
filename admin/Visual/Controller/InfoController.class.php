<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\InfoModel as InfoModel;

class InfoController extends Controller {

	public function _initialize(){
		$this->info = D('Info');
	}

	## 
	public function infoSummary()
	{
		$this->info->getSummary();
	}

	
}
