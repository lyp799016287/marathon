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
		
		$role_id = I('role_id', 0, 'intval');

		$ssql = "SELECT id AS node_id, name, title, pid, level FROM think_node WHERE STATUS = 1";
		$rrs = queryByNoModel('think_node', '', 'DB_ADMIN', $ssql);
		//var_dump($rrs);

		if($rrs === false){
			$this->ajaxReturn(1, 'JSON');
		}else{
			$sql = "SELECT a.role_id, b.id AS node_id, b.name, b.title, b.pid, b.level FROM think_access a LEFT JOIN think_node b ON a.node_id=b.id WHERE b.status =1 AND a.role_id = ".$role_id;

			$rs = queryByNoModel('think_access', '', 'DB_ADMIN', $sql);

			if($rs === false){
				$this->ajaxReturn(1, 'JSON');
			}else{
				foreach($rrs as &$node){
					foreach($rs as $access){
						if(($node['node_id'] == $access['node_id']) && ($access['level'] == 3)){
							$node['ischecked'] = 'true';
						}
					}
				}

				$this->ajaxReturn($rrs, 'JSON');
			}
		}
	}

	public function setAccess(){

		$role_id = I('role_id', 0, 'intval');
		$access_arr = I('privilege_id');
		

		if(empty($access_arr)){
			$this->ajaxReturn(1, 'JSON');
		}

		$del = "DELETE FROM think_access WHERE role_id = ".$role_id;
		$rs = execByNoModel('think_access', '', 'DB_ADMIN', $del);

		$second = array();

		foreach($access_arr as $access){
			$tmp = explode("-", $access);	//参数为node_id-pid(三级id-二级id or 二级id-一级id)

			if(empty($tmp[1])){
				$fdata = array(
					'role_id' => $role_id,
					'node_id' => $tmp[0],
					'level'	=> 1,
					'module' => $tmp[1]	
				);
				$frs = insertByNoModel('think_access', '', 'DB_ADMIN', $fdata);
				
				continue;
			}
			
			if(!in_array($tmp[1], $second)){
				
				$select = "SELECT pid FROM think_node WHERE id = ".$tmp[1];
				$rrs = queryByNoModel('think_node', '', 'DB_ADMIN', $select);

				$rs = 0;

				if($rrs){
					$data = array(
						'role_id' => $role_id,
						'node_id' => $rrs[0]['pid'],
						'level'	=> 1,
						'module' => 0
					);

					$rs = insertByNoModel('think_access', '', 'DB_ADMIN', $data);
				}
				
				if(!$rs){
					$this->ajaxReturn(1, 'JSON');
				}
				
				$sdata = array(
					'role_id' => $role_id,
					'node_id' => $tmp[1],
					'level'	=> 2,
					'module' => $rrs[0]['pid']	
				);
				$srs = insertByNoModel('think_access', '', 'DB_ADMIN', $sdata);
				
				if(!$srs){
					$this->ajaxReturn(1, 'JSON');
				}

				array_push($second, $tmp[1]);

			}else{
				$srs = $tmp[1];
			}

			$tdata = array(
				'role_id' => $role_id,
				'node_id' => $tmp[0],
				'level'	=> 3,
				'module' => $tmp[1]	
			);
			$trs = insertByNoModel('think_access', '', 'DB_ADMIN', $tdata);
		}

		if($trs){
			$this->ajaxReturn(0, 'JSON');
		}else{
			$this->ajaxReturn(1, 'JSON');
		}
	}
}


//End Of Script

