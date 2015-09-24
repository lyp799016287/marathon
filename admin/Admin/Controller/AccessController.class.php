<?php
/**
 * MenuController.class.php
 * 对DB:t_Menu的增、查、删、改等操作
 * 中间层，数据的增、查、删、改
 * @author mandyzhou
 */
namespace Admin\Controller;
use Think\Controller;

class AccessController extends Controller {
	
	public function _initialize(){
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
	}

	public function show(){
		$this->display('AccessManage');
	}

	public function selectAll(){
		$where = '1';
		$sql = "SELECT id, title, pid, level FROM think_node WHERE status = 1";
		$ret = queryByNoModel('think_node', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}
}