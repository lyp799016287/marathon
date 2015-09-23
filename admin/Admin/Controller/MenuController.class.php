<?php
/**
 * MenuController.class.php
 * 对DB:t_Menu的增、查、删、改等操作
 * 中间层，数据的增、查、删、改
 * @author
 */
namespace Admin\Controller;
use Think\Controller;

class MenuController extends Controller {
	
	public function show(){
		$this->display('MenuManage');
	}

	public function modify(){
		$this->display("ModifyMenu");
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

		

		//set ip
		$IP=getenv("REMOTE_ADDR");
		$data['create_ip']=$IP;
		//set create user
		$data['create_id']=$_COOKIE['CurrentUserID'];

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
			if($menu_id > 1000000000 || $menu_id < -1000000000){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['menu_id'] = $menu_id;
		}
		if(isset($_REQUEST['menu_name'])) {
			$menu_name = $_REQUEST['menu_name'];
			$menu_nameLen = strlen($menu_name);
			if($menu_nameLen > 32 || $menu_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['menu_name'] = $menu_name;
		}
		if(isset($_REQUEST['menu_desc'])) {
			$menu_desc = $_REQUEST['menu_desc'];
			$menu_descLen = strlen($menu_desc);
			if($menu_descLen > 128 || $menu_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['menu_desc'] = $menu_desc;
		}
		if(isset($_REQUEST['menu_url'])) {
			$menu_url = $_REQUEST['menu_url'];
		//    $menu_url = str_replace('$','&',$menu_url);
			$menu_urlLen = strlen($menu_url);
			if($menu_urlLen > 128 || $menu_urlLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['menu_url'] = $menu_url;
		}
		if(isset($_REQUEST['menu_tabid'])) {
			$menu_tabid = $_REQUEST['menu_tabid'];
			$menu_tabidLen = strlen($menu_tabid);
			if($menu_tabidLen > 64 || $menu_tabidLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['menu_tabid'] = $menu_tabid;
		}
		if(isset($_REQUEST['isdefault'])) {
			$isdefault = $_REQUEST['isdefault'];
			if($isdefault==true)
			{
				$data['isdefault']=1;
			}
			else
			{
				$data['isdefault']=0;
			}
		}
		if(isset($_REQUEST['sortorder'])) {
			$sortorder = $_REQUEST['sortorder'];
			$sortorderLen = strlen($sortorder);
			if($sortorderLen > 64 || $sortorderLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['sortorder'] = $sortorder;
		}
		if(isset($_REQUEST['accordion'])) {
			$accordion = $_REQUEST['accordion'];
			$accordionLen = strlen($accordion);
			if($accordionLen > 128 || $accordionLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$data['accordion'] = $accordion;
		}

		$update = "UPDATE t_menu SET menu_name = '".$data['menu_name']."', menu_desc = '".$data['menu_desc']."', menu_url = '".$data['menu_url']."', menu_tabid = '".$data['menu_tabid']."', isdefault = '".$data['isdefault']."', sortorder = '".$data['sortorder']."', accordion = '".$data['accordion']."' WHERE menu_id={$menu_id}";
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
		if($menu_id > 1000000000 || $menu_id < -1000000000){
			$this->ajaxReturn(1, 'JSON');
		}
		$fwhere  = "SELECT * FROM t_menu WHERE menu_fid='{$menu_id}'";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $fwhere);

		if($retVal) {
			$this->ajaxReturn(2, 'JSON');
		}
		//open transation
		$retVal = ITrans::begin();
		if($retVal == false)
		{
			//开启事务失败
			ITrans::rollback();
			$this->ajaxReturn(1, 'JSON');

		}
		//get content
		$where  = "menu_id='{$menu_id}'";
		$retVal = IMenuDao::getRows('', $where, 0, 1);
		if($retVal == false) {
			ITrans::rollback();
			$this->ajaxReturn(1, 'JSON');
		}
		//add item to deletelog table
		if(AddItem('t_Menu',serialize($retVal))!=0)
		{
			ITrans::rollback();
			$this->ajaxReturn(1, 'JSON');
		}
		//delete item
		$where  = "menu_id={$menu_id}";
		$retVal = IMenuDao::remove($where);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		// submit transation
		$retVal =ITrans::commit();
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn(0, 'JSON');
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

		$sql = "SELECT * FROM t_menu WHERE menu_id={$menu_id}";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($retVal, 'JSON');
	}

	/**
	 * 查询多条记录,包括分页
	 * @return var
	 */
	function menulist()
	{
		$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
		$page = intval($page);

		$pagesize = (isset($_REQUEST['pagesize']) ? $_REQUEST['pagesize'] : 20);
		$pagesize = intval($pagesize);

		$where = '1';
		if(!empty($_REQUEST['menu_id'])) {
			$menu_id = $_REQUEST['menu_id'];
			$menu_id = intval($menu_id);
			$where .= " AND menu_id={$menu_id}";
		}
		if(!empty($_REQUEST['menu_name'])) {
			$menu_name = $_REQUEST['menu_name'];
			$menu_nameLen = strlen($menu_name);
			if($menu_nameLen > 32 || $menu_nameLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND menu_name='{$menu_name}'";
		}
		if(!empty($_REQUEST['menu_desc'])) {
			$menu_desc = $_REQUEST['menu_desc'];
			$menu_descLen = strlen($menu_desc);
			if($menu_descLen > 128 || $menu_descLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND menu_desc='{$menu_desc}'";
		}
		if(!empty($_REQUEST['menu_url'])) {
			$menu_url = $_REQUEST['menu_url'];
			$menu_urlLen = strlen($menu_url);
			if($menu_urlLen > 128 || $menu_urlLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND menu_url='{$menu_url}'";
		}
		if(!empty($_REQUEST['menu_tabid'])) {
			$menu_tabid = $_REQUEST['menu_tabid'];
			$menu_tabidLen = strlen($menu_tabid);
			if($menu_tabidLen > 64 || $menu_tabidLen < 0){
				$this->ajaxReturn(1, 'JSON');
			}
			$where .= " AND menu_tabid='{$menu_tabid}'";
		}
		if(!empty($_REQUEST['menu_fid'])) {
			$menu_fid = $_REQUEST['menu_fid'];
			$menu_fid = intval($menu_fid);
			$where .= " AND menu_fid={$menu_fid}";
		}

		$sql = "SELECT * FROM t_menu WHERE 1=1 ".$where;
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($retVal, 'JSON');
	}

	function selectAll()
	{
		$sql = "SELECT * FROM t_menu";
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		$this->ajaxReturn($retVal, 'JSON');;
	}

	function selectByRoleId()
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
		$list=$this->selectAll();
		//处理数据中的特殊字符
		$where  = "role_id='{$role_id}'";

		$sql = "SELECT a.*, b.* FROM t_menu a LEFT JOIN t_role_menu_relation b ON a.menu_id = b.menu_id WHERE ".$where;
		$retVal = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		if($retVal == false) {
			$this->ajaxReturn($list, 'JSON');
		}
		$HaveMenus=array();
		foreach($retVal as $row)
		{
			array_push($HaveMenus,$row['menu_id']);
		}
		$result= array();
		foreach($list as $row){
			if(in_array($row['menu_id'],$HaveMenus))
			{
				$row['ischecked']=true;
			}
			array_push($result,$row);
		}
		$this->ajaxReturn($result, 'JSON');
	}

	function SelectByUserID($user_id)
	{
		//get roleids
		$where  = "user_id='{$user_id}'";
		$sql = "SELECT * FROM t_user_role_relation WHERE ".$where;
		$roleids = queryByNoModel('t_user_role_relation', '', 'DB_ADMIN', $sql);
		if($roleids == false) {
			$this->ajaxReturn(1, 'JSON');
		}

		unset($sql);
		unset($where);

		$menus=array();
		//get menuids
		foreach($roleids as $row)
		{
			$where  = "role_id={$row['role_id']}";
			$sql = "SELECT * FROM t_role_menu_relation WHERE ".$where;
			$menuids = queryByNoModel('t_role_menu_relation', '', 'DB_ADMIN', $sql);
			if($menuids != false) {
				foreach($menuids as $row)
				{
					array_push($menus,$row['menu_id']);
				}
			}
		}
		//return $menus;
		$menus=array_unique($menus);
		$result=array();

		unset($sql);
		unset($where);

		if(!empty($menus)){
			if(count($menus)==1){
				$menu_str = $menus;
			}else{
				$menu_str = implode(",", $menus);
			}
			$where .= " AND menu_id in (".$menu_str.")";
		}

		$sql = "SELECT menu_id,menu_name,menu_desc,menu_url,menu_tabid,menu_fid,sortorder FROM t_menu WHERE ".$where." ORDER BY menu_fid ASC,sortorder ASC";
		$result = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);
		$this->ajaxReturn($result, 'JSON');
	}

	function getMenu()
	{
		if(isset($_COOKIE['CurrentUserID']))
		{
			$user_id=$_COOKIE['CurrentUserID'];
		}
		else
		{
			$this->ajaxReturn(1, 'JSON');
		}
		$this->SelectByUserID($user_id);
	}

	function NewSelectByUserID($user_id)
	{
		//get roleids
		$where  = "user_id='{$user_id}'";
		$sql = "SELECT * FROM t_user_role_relation WHERE ".$where;
		$roleids = queryByNoModel('t_user_role_relation', '', 'DB_ADMIN', $sql);

		if($roleids == false) {
			$this->ajaxReturn(1, 'JSON');
		}
		

		$menus=array();
		//get menuids
		foreach($roleids as $row)
		{
			$where  = "role_id={$row['role_id']}";
			$sql = "SELECT * FROM t_role_menu_relation WHERE ".$where;
			$menuids = queryByNoModel('t_role_menu_relation', '', 'DB_ADMIN', $sql);
			if($menuids != false) {
				foreach($menuids as $row)
				{
					array_push($menus,$row['menu_id']);
				}
			}
		}
		//return $menus;
		$menus=array_unique($menus);
		$result=array();
		
		if(!empty($menus)){
			if(count($menus)==1){
				$menu_str = $menus;
			}else{
				$menu_str = implode(",", $menus);
			}
			$where .= " AND menu_id in (".$menu_str.")";
		}

		$sql = "SELECT menu_id,menu_name,menu_desc,menu_url,menu_tabid,menu_fid,sortorder FROM t_menu WHERE ".$where." ORDER BY menu_fid ASC,sortorder ASC";
		$result = queryByNoModel('t_menu', '', 'DB_ADMIN', $sql);

		$lv1 = array();
		$lv1key = array();
		$lv2 = array();
		$lv2key = array();
		$lv3 = array();
		foreach($result as $key => $row)
		{
			if( '0'===$row['menu_fid'])
			{
				$lv1[] = $row;
				$lv1key[]= $row['menu_id'];
				unset($result[$key]);
			}
		}

		foreach($result as $key => $row)
		{
			if( in_array( $row['menu_fid'],$lv1key))
			{
				$lv2[] = $row;
				$lv2key[]= $row['menu_id'];
				unset($result[$key]);
			}
		}

		foreach($result as $key => $row)
		{
			if( in_array( $row['menu_fid'],$lv2key))
			{
				$lv3[] = $row;
			}
		}

		$rs = array('lv1'=>$lv1,'lv2'=>$lv2,'lv3'=>$lv3);
		$_SESSION["menu"] = serialize($rs);
		$this->ajaxReturn($rs, 'JSON');
	}

	function getNewMenu()
	{
		if(isset($_COOKIE['CurrentUserID']))
		{
			$user_id=$_COOKIE['CurrentUserID'];
		}else{
			$this->ajaxReturn(1, 'JSON');
		}

		if(isset($_SESSION["menu"])){
			unserialize($_SESSION["menu"]);
		}else{
			$this->NewSelectByUserID($user_id);
		}
	}
}

//End Of Script