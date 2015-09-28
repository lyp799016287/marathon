<?php
/**
 * RoleController.class.php
 * 对DB:t_role的增、查、删、改等操作
 * 中间层，数据的增、查、删、改
 * @author
 */
namespace Admin\Controller;
use Think\Controller;

class UserController extends Controller {
	
	public function _initialize(){
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
	}

	public function show(){
		$this->display("UserManage");
	}
	
	public function selectAll(){

		$where = '1';
		$sql = "SELECT * FROM t_user WHERE status = 1";
		$ret = queryByNoModel('think_role', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}
}