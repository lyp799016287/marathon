<?php
namespace Visual\Controller;
use Think\Controller;
use Visual\Model\PortModel as PortModel;

class PortController extends Controller {

	public function _initialize()
	{
		$this->port = D('Port');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		// $this->assign("index", 7);
	}
	
	public function show()
	{
		$this->display("statPort");
	}

	## 趋势图对应的数据接口
	public function portSummary()
	{
		$type = I('type', 1, 'intval');
		$result = $this->port->calPeriod($type);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}

	## 列表对应的数据接口
	public function portDetail()
	{
		$data = array();
		$data['current_page'] = I('current_page', 1, 'intval');
		$data['page_size'] = I('page_size', 20, 'intval');
		$data['sort_name'] = I('sort_name');
		$data['sort_order'] = I('sort_order');

		$result = $this->port->calDetail($data);
		if($result === false)
			$this->ajaxReturn(array('code'=>-1));
		else
			$this->ajaxReturn(array('code'=>1, 'data'=>$result));
	}
	
}
