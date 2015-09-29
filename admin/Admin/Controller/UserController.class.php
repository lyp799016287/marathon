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

	public function userList(){
		$where = '1';
		
		$sql = "SELECT * FROM t_user WHERE STATUS != -1 ORDER BY create_time DESC";
		$ret = queryByNoModel('t_user', '', 'DB_ADMIN', $sql);
		
		$curr_page = I('page', 1, 'intval');
		$page_size = 20;

		if(!empty($ret)){
			$limit = ($curr_page - 1)*$page_size;
			$data = array_slice($ret, $limit, $page_size);
		}
		
		$total = count($ret);
		$total_num = ceil($total/$page_size);
		$this->assign("list", $data);
		$this->assign("total", $total);
		$this->assign("current", $curr_page);
		$this->assign("total_num", $total_num);
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

	public function userDel(){
		
		$id = I('id', 0, 'intval');

		$sql = "UPDATE t_user SET status = -1, modify_time = NOW() WHERE id = ".$id;
		$rs = updateByNoModel('t_user', '', 'DB_ADMIN', $sql);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'删除失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'删除成功'), 'JSON');
		}
	}

	public function userChk(){
		
		$id = I('id', 0, 'intval');
		
		$sql = "UPDATE t_user SET status = 2, modify_time = NOW() WHERE id = ".$id;
		$rs = updateByNoModel('t_user', '', 'DB_ADMIN', $sql);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'审核失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'审核成功'), 'JSON');
		}
	}
}