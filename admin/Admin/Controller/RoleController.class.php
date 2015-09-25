<?php
/**
 * RoleController.class.php
 * 对DB:t_role的增、查、删、改等操作
 * 中间层，数据的增、查、删、改
 * @author
 */
namespace Admin\Controller;
use Think\Controller;

class RoleController extends Controller {

	public function _initialize(){
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
	}

	public function show(){
		$this->display('RoleManage');
	}

	/**
	 * 新增一条记录
	 * @return var
	 */
	public function add()
	{
		if(!isset($_REQUEST['role_name']) || !isset($_REQUEST['role_desc'])){
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();

		//取得用户提交的数据
		if(isset($_REQUEST['role_name'])) {
			$role_name = $_REQUEST['role_name'];
			$role_nameLen = strlen($role_name);
			if($role_nameLen > 128 || $role_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $role_name;
		}
		if(isset($_REQUEST['role_desc'])) {
			$role_desc = $_REQUEST['role_desc'];
			$role_descLen = strlen($role_desc);
			if($role_descLen > 128 || $role_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['remark'] = $role_desc;
		}

		$data['status'] = 1;
		
		//处理数据中的特殊字符
		$ret = insertByNoModel('think_role', '', 'DB_ADMIN', $data);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn(0, 'JSON');
	}

	/**
	 * 修改一条记录
	 * @return var
	 */
	public function edit()
	{
		if(!isset($_REQUEST['role_name'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();
		//取得用户提交的数据
		if(isset($_REQUEST['tr_role_id'])) {
			$tr_role_id = $_REQUEST['tr_role_id'];
			$tr_role_idLen = strlen($tr_role_id);
			if($tr_role_idLen > 32 || $tr_role_idLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['id'] = $tr_role_id;
		}
		if(isset($_REQUEST['role_name'])) {
			$role_name = $_REQUEST['role_name'];
			$role_nameLen = strlen($role_name);
			if($role_nameLen > 32 || $role_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $role_name;
		}
		if(isset($_REQUEST['role_desc'])) {
			$role_desc = $_REQUEST['role_desc'];
			$role_descLen = strlen($role_desc);
			if($role_descLen > 128 || $role_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['remark'] = $role_desc;
		}
		$sql = "UPDATE think_role SET name='{$role_name}', remark='{$role_desc}' WHERE id='{$tr_role_id}'";
		$ret = execByNoModel('think_role', '', 'DB_ADMIN', $sql);
		if($ret === false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn(0, 'JSON');
	}

	/**
	 * 删除一条记录
	 * @return var
	 */
	public function rmv()
	{
		if(!isset($_REQUEST['role_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$role_id = $_REQUEST['role_id'];
	
		//delete item
		//处理数据中的特殊字符
		$where  = "UPDATE think_role SET status = 2 WHERE id='{$role_id}'";
		$ret = execByNoModel('think_role', '', 'DB_ADMIN', $where);
		if($ret === false) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		$this->ajaxReturn(0, 'JSON');
	}

	public function selectAll(){
		$where = '1';
		$sql = "SELECT * FROM think_role WHERE status = 1";
		$ret = queryByNoModel('think_role', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}

	/**
	 * 查询一条记录
	 * @return var
	 */
	public function detail(){
		$where = '';
		if(!isset($_REQUEST['role_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		if(!empty($_REQUEST['role_id'])) {
			$role_id = $_REQUEST['role_id'];
			$role_idLen = strlen($role_id);
			if($role_idLen > 32 || $role_idLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND id='{$role_id}'";
		}

		$sql = "SELECT * FROM think_role WHERE status = 1 ".$where;
		$ret = queryByNoModel('think_role', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}

	public function modifyPrivilege(){
		$this->display("ModifyPrivilege");
	}

	public function getAccess(){
	
	}
}


//End Of Script

