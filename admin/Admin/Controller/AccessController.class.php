<?php
/**
 * AccessController.class.php
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
		$sql = "SELECT id, name, title, pid, remark, level FROM think_node WHERE status = 1";
		$ret = queryByNoModel('think_node', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}

	/**
	 * 新增一条记录
	 * @return var
	 */
	function add()
	{
		if(!isset($_REQUEST['privilege_name']) || !isset($_REQUEST['privilege_desc'])){
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();

		//取得用户提交的数据
		if(isset($_REQUEST['privilege_name'])) {
			$privilege_name = $_REQUEST['privilege_name'];
			$privilege_nameLen = strlen($privilege_name);
			if($privilege_nameLen > 128 || $privilege_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $privilege_name;
		}
		if(isset($_REQUEST['privilege_desc'])) {
			$privilege_desc = $_REQUEST['privilege_desc'];
			$privilege_descLen = strlen($privilege_desc);
			if($privilege_descLen > 128 || $privilege_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['title'] = $privilege_desc;
		}

		if(isset($_REQUEST['privilege_remark'])) {
			$privilege_remark = $_REQUEST['privilege_remark'];
			$privilege_remarkLen = strlen($privilege_remark);
			if($privilege_remarkLen > 128 || $privilege_remarkLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['remark'] = $privilege_remark;
		}

		if(isset($_REQUEST['privilege_fname'])) {
			$privilege_fid = intval($_REQUEST['privilege_fname']);
			$data['pid'] = $privilege_fid;
		}

		if(isset($_REQUEST['privilege_level'])) {
			$privilege_level = intval($_REQUEST['privilege_level']);
			$data['level'] = $privilege_level;
		}

		$data['status'] = 1;
		
		//处理数据中的特殊字符
		$ret = insertByNoModel('think_node', '', 'DB_ADMIN', $data);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn(0, 'JSON');
	}

	/**
	 * 修改一条记录
	 * @return var
	 */
	function edit()
	{
		if(!isset($_REQUEST['privilege_name'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();
		//取得用户提交的数据
		if(isset($_REQUEST['privilege_id'])) {
			$privilege_id = $_REQUEST['privilege_id'];
			$data['id'] = $privilege_id;
		}
		if(isset($_REQUEST['privilege_name'])) {
			$privilege_name = $_REQUEST['privilege_name'];
			$privilege_nameLen = strlen($privilege_name);
			if($privilege_nameLen > 32 || $privilege_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $privilege_name;
		}
		if(isset($_REQUEST['privilege_desc'])) {
			$privilege_desc = $_REQUEST['privilege_desc'];
			$privilege_descLen = strlen($privilege_desc);
			if($privilege_descLen > 128 || $privilege_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['title'] = $privilege_desc;
		}
		if(isset($_REQUEST['privilege_level'])) {
			$privilege_level = $_REQUEST['privilege_level'];
			$data['level'] = $privilege_level;
		}
		if(isset($_REQUEST['privilege_remark'])) {
			$privilege_remark = $_REQUEST['privilege_remark'];
			$privilege_remarkLen = strlen($privilege_remark);
			if($privilege_remarkLen > 32 || $privilege_remarkLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['remark'] = $privilege_remark;
		}
		$sql = "UPDATE think_node SET name='{$privilege_name}', title='{$privilege_desc}', remark='{$privilege_remark}', level={$privilege_level} WHERE id='{$privilege_id}'";
		$ret = execByNoModel('think_node', '', 'DB_ADMIN', $sql);
		if($ret === false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn(0, 'JSON');
	}

	/**
	 * 删除一条记录
	 * @return var
	 */
	function rmv()
	{
		if(!isset($_REQUEST['privilege_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$privilege_id = intval($_REQUEST['privilege_id']);
	
		$sql = "SELECT COUNT(*) AS num FROM think_node WHERE pid = ".$privilege_id;
		$rs = queryByNoModel('think_node', '', 'DB_ADMIN', $sql);
		if(empty($rs)){
			$this->ajaxReturn(1, 'JSON');
		}

		if(!empty($rs[0]['num'])){
			$this->ajaxReturn(2, 'JSON');
		}

		//delete item
		//处理数据中的特殊字符
		$where  = "UPDATE think_node SET status = 2 WHERE id='{$privilege_id}'";
		$ret = execByNoModel('think_node', '', 'DB_ADMIN', $where);
		if($ret === false) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		$this->ajaxReturn(0, 'JSON');
	}

	/**
	 * 查询一条记录
	 * @return var
	 */
	function detail(){
		$where = '';
		if(!isset($_REQUEST['privilege_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		if(!empty($_REQUEST['privilege_id'])) {
			$privilege_id = $_REQUEST['privilege_id'];
			$privilege_idLen = strlen($privilege_id);
			if($privilege_idLen > 32 || $privilege_idLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND id='{$privilege_id}'";
		}

		$sql = "SELECT * FROM think_node WHERE status = 1 ".$where;
		$ret = queryByNoModel('think_node', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}
}