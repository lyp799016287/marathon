<?php
/**
 * MenuController.class.php
 * 对DB:t_Menu的增、查、删、改等操作
 * 中间层，数据的增、查、删、改
 * @author mandyzhou
 */
namespace Admin\Controller;
use Think\Controller;

class MenuController extends Controller {

	public function _initialize(){
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
	}
	
	public function show(){
		$this->display('MenuManage');
	}

	/**
	 * 新增一条记录
	 * @return var
	 */
	function add()
	{
		if(!isset($_REQUEST['menu_name'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();

		if(isset($_REQUEST['menu_name'])) {
			$menu_name = $_REQUEST['menu_name'];
			$menu_nameLen = strlen($menu_name);
			if($menu_nameLen > 32 || $menu_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $menu_name;
		}
		
		if(isset($_REQUEST['menu_url'])) {
			$menu_url = $_REQUEST['menu_url'];
			$menu_urlLen = strlen($menu_url);
			if($menu_urlLen > 128 || $menu_urlLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['url'] = $menu_url;
		}
		
		if(isset($_REQUEST['menu_fid'])) {
			$menu_fid = $_REQUEST['menu_fid'];
			$menu_fid = intval($menu_fid);
			$data['parent_id'] = $menu_fid;
		}

		//set create user
		$data['admin_id']= $_SESSION[C('USER_AUTH_KEY')];

		$data['status'] = 1;

		//处理数据中的特殊字符
		$retVal = insertByNoModel('t_menu', '', 'DB_ADMIN', $data);
		if($retVal == false) {
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
		if(!isset($_REQUEST['menu_id'])||!isset($_REQUEST['menu_name'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		$data = array();
		//取得用户提交的数据
		if(isset($_REQUEST['menu_id'])) {
			$menu_id = $_REQUEST['menu_id'];
			$menu_id = intval($menu_id);
			$data['id'] = $menu_id;
		}
		if(isset($_REQUEST['menu_name'])) {
			$menu_name = $_REQUEST['menu_name'];
			$menu_nameLen = strlen($menu_name);
			if($menu_nameLen > 32 || $menu_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['name'] = $menu_name;
		}
		if(isset($_REQUEST['menu_url'])) {
			$menu_url = $_REQUEST['menu_url'];
		//    $menu_url = str_replace('$','&',$menu_url);
			$menu_urlLen = strlen($menu_url);
			if($menu_urlLen > 128 || $menu_urlLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['url'] = $menu_url;
		}

		$update = "UPDATE t_menu SET name = '".$data['name']."', url = '".$data['url']."' WHERE id={$menu_id}";
		$retVal = execByNoModel('t_menu', '', 'DB_ADMIN', $update);
		if($retVal === false) {
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
		if(!isset($_REQUEST['menu_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$menu_id = $_REQUEST['menu_id'];
		$menu_id = intval($menu_id);
		$fwhere  = "SELECT * FROM t_menu WHERE parent_id='{$menu_id}'";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $fwhere);

		if($retVal) {
			$this->ajaxReturn(2, 'JSON');
		}
		
		//get content
		
		$sql = "UPDATE t_menu SET status = 2, modify_time = NOW() WHERE id = ".$menu_id;
		$rs = updateByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if(!$rs){
			$this->ajaxReturn(3, 'JSON');
		}else{
			$this->ajaxReturn(0, 'JSON');
		}
	}

	/**
	 * 查询一条记录
	 * @return var
	 */
	function detl()
	{
		if(!isset($_REQUEST['menu_id'])) {
			$this->ajaxReturn(1, 'JSON');
		}
		//取得用户提交的数据
		$menu_id = $_REQUEST['menu_id'];
		$menu_id = intval($menu_id);

		$sql = "SELECT * FROM t_menu WHERE id={$menu_id}";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($retVal, 'JSON');
	}


	function selectAll()
	{
		$sql = "SELECT id, name, url, parent_id FROM t_menu WHERE status = 1";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($retVal, 'JSON');;
	}

}

//End Of Script