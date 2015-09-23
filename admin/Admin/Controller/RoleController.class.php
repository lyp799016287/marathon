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

	public function show(){
		$this->display('RoleManage');
	}

	public function modify(){
		$this->display("ModifyRole");
	}

	/**
	 * 新增一条记录
	 * @return var
	 */
	function add()
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
			$data['role_name'] = $role_name;
		}
		if(isset($_REQUEST['role_desc'])) {
			$role_desc = $_REQUEST['role_desc'];
			$role_descLen = strlen($role_desc);
			if($role_descLen > 128 || $role_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['role_desc'] = $role_desc;
		}
		//set ip
		$IP=getenv("REMOTE_ADDR");
		$data['create_ip']=$IP;
		//set create user
		$data['create_id']=$_COOKIE['CurrentUserID'];

		//处理数据中的特殊字符
		$ret = insertByNoModel('t_role', '', 'DB_ADMIN', $data);
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
			$data['role_id'] = $tr_role_id;
		}
		if(isset($_REQUEST['role_name'])) {
			$role_name = $_REQUEST['role_name'];
			$role_nameLen = strlen($role_name);
			if($role_nameLen > 32 || $role_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['role_name'] = $role_name;
		}
		if(isset($_REQUEST['role_desc'])) {
			$role_desc = $_REQUEST['role_desc'];
			$role_descLen = strlen($role_desc);
			if($role_descLen > 128 || $role_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['role_desc'] = $role_desc;
		}
		$sql = "UPDATE t_role SET role_name='{$role_name}', role_desc='{$role_desc}' WHERE role_id='{$tr_role_id}'";
		$ret = execByNoModel('t_role', '', 'DB_ADMIN', $sql);
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
		if(!isset($_REQUEST['role_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$role_id = $_REQUEST['role_id'];
		$role_idLen = strlen($role_id);
		if($role_idLen > 32 || $role_idLen < 0){
			$this->ajaxReturn(1, 'JSON');
		}
		
		//get content
		$sql = "SELECT * FROM t_role WHERE role_id='{$role_id}'";
		$retVal = queryByNoModel('t_role', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		//delete item
		//处理数据中的特殊字符
		$where  = "DELETE FROM t_role WHERE role_id='{$role_id}'";
		$ret = execByNoModel('t_role', '', 'DB_ADMIN', $where);
		if($ret === false) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		$this->ajaxReturn(0, 'JSON');
	}

	function selectAll(){
		$where = '1';
		$sql = "SELECT * FROM t_role";
		$ret = queryByNoModel('t_role', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}

	/**
	 * 查询一条记录
	 * @return var
	 */
	function detail(){
		$where = '1';
		if(!isset($_REQUEST['role_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		
		if(!empty($_REQUEST['role_id'])) {
			$role_id = $_REQUEST['role_id'];
			$role_idLen = strlen($role_id);
			if($role_idLen > 32 || $role_idLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND role_id='{$role_id}'";
		}

		$sql = "SELECT * FROM t_role WHERE ".$where;
		$ret = queryByNoModel('t_role', '', 'DB_ADMIN', $sql);
		if($ret == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($ret, 'JSON');
	}
	/*
	function role_rightdetail(){
		$result=array();
		$where = '1';
		if(!isset($_REQUEST['role_id'])) {
			return 1;
		}
		if(!empty($_REQUEST['role_id'])) {
			$tr_role_id = $_REQUEST['role_id'];
			$tr_role_idLen = strlen($tr_role_id);
			if($tr_role_idLen > 32 || $tr_role_idLen < 0){
				return 1;
			}
			$where .= " AND role_id='{$tr_role_id}'";
		}
		$ret = IrolePrivilegeRelationDao::getRows('', $where, '', '');
		if($ret == false) {
			return 1;
		}

		foreach($ret as $lid)
		{
			$where = " privilege_id='{$lid['privilege_id']}'";
			$retVal = IPrivilegeDao::getRows('', $where, '', '');
			if($retVal == false) {
				continue;
			}
			array_push($result,$retVal[0]);
		}
		return $result;

	}*/

	function selectByDepartId(){
		if(!isset($_REQUEST['depart_fid'])||!isset($_REQUEST['depart_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$depart_id = $_REQUEST['depart_id'];
		$depart_idLen = strlen($depart_id);
		if($depart_idLen > 32 || $depart_idLen < 0){
			$this->ajaxReturn(1, 'JSON');
		}
		$depart_fid = $_REQUEST['depart_fid'];
		$depart_fidLen = strlen($depart_fid);
		if($depart_fidLen > 32 || $depart_fidLen < 0){
			$this->ajaxReturn(1, 'JSON');
		}
		//if the father depart's id is 0,show all privilege;otherwise show father's role;
		$list= array();
		if($depart_fid!=0)
		{
			//处理数据中的特殊字符
			$where  = "SELECT * FROM t_depart WHERE depart_id='{$depart_fid}'";
			$retVal = queryByNoModel('t_depart', '', 'DB_ADMIN', $where);
			if($retVal == false) {
				$this->ajaxReturn(1, 'JSON');
			}
			foreach($retVal as $row){
				$where  = "SELECT * FROM t_role WHERE role_id='{$row['role_id']}'";

				$retVal = queryByNoModel('t_role', '', 'DB_ADMIN', $where);
				if($retVal == false) {
					$this->ajaxReturn(1, 'JSON');
				}
				array_push($list,$retVal[0]);
			}
		}else{
			$sql = "SELECT * FROM t_role";
			$list = queryByNoModel('t_role', '', 'DB_ADMIN', $sql);
		}

		//get the selected role
		$sql = "SELECT * FROM t_depart_role_relation WHERE ";
		$where  = $sql."depart_id='{$depart_id}'";
		$retVal = queryByNoModel('t_depart_role_relation', '', 'DB_ADMIN', $where);
		if($retVal == false) {
			$this->ajaxReturn($list, 'JSON');
		}
		
		$selectRoles=array();
		
		unset($where);
		unset($sql);
		
		$sql = "SELECT * FROM t_role WHERE ";
		foreach($retVal as $row){
			$where  = $sql."role_id='{$row['role_id']}'";

			$ret = queryByNoModel('t_role', '', 'DB_ADMIN', $where);
			if($ret == false) {
				continue;
			}
			if (!in_array($ret[0], $list)) {
				array_push($list,$ret[0]);
			}
			array_push($selectRoles,$row['role_id']);
		}
		$result=array();
		foreach($list as $row)
		{
			if(in_array($row['role_id'],$selectRoles))
			{
				$row['ischecked']=true;
			}
			array_push($result,$row);
		}

		$this->ajaxReturn($result, 'JSON');
	}

	/*function role_selectByuserId(){
		if(!isset($_REQUEST['user_id'])) {
			return 1;
		}
		//取得用户提交的数据
		$user_id = $_REQUEST['user_id'];
		$user_idLen = strlen($user_id);
		if($user_idLen > 32 || $user_idLen < 0){
			return 1;
		}

		$list= array();
		$list= role_selectAll();

		//get the selected role
		$where  = "user_id='{$user_id}'";
		$retVal = IUserRoleRelationDao::getRows('', $where, 0, 0);
		if($retVal == false) {
			return $list;
		}
		$selectRoles=array();
		foreach($retVal as $row)
		{
			array_push($selectRoles,$row['role_id']);
		}
		$result=array();
		foreach($list as $row)
		{
			if(in_array($row['role_id'],$selectRoles))
			{
				$row['ischecked']=true;
			}
			array_push($result,$row);
		}
		return $result;
	}*/
}


//End Of Script

