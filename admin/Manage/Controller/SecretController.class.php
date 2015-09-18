<?php
namespace Manage\Controller;
use Think\Controller;
class SecretController extends Controller {

	protected $secret =  null;

	public function _initialize(){
		$this->secret = D('Secret');
		$this->assign("menu_path", C("MENU_PATH"));
	}

	/**秘密列表**/
    public function SecretList(){
        $querypara='';
		$curr_page = I('page', 1, 'intval');
		$page_size = 20;
        
		$datapara = array(
			'keyword'		=> I('keyword'),
			'bgn_date'		=> I('bgn_date', ''),
			'end_date'		=> I('end_date', ''),
			'status'    	=> I('status',1,'intval'),
			
		);
        foreach($datapara as $key => $val){
            $querypara =$querypara.'&'.$key.'='.$val;
        }        
		//echo var_dump($data);
		
		/************排除被举报的秘贴*********/
		$rrs = $this->secret->getSecretReport();

		$except = array();

		if(!empty($rrs)){
			foreach($rrs as $val){
				array_push($except, $val['secret_id']);	
			}
		}
		$datapara['except'] = implode(",", $except);
        
		$rs = $this->secret->getSecretList($datapara);

		$data = array();
		if(!empty($rs)){
			$limit = ($curr_page - 1)*$page_size;
			$data = array_slice($rs, $limit, $page_size);
		}
		
		$total = count($rs);
		$total_num = ceil($total/$page_size);
		$this->assign("list", $data);
		$this->assign("total", $total);
		$this->assign("current", $curr_page);
		$this->assign("total_num", $total_num);
		$this->assign("keyword", $datapara['keyword']);
		$this->assign("bgn_date", $datapara['bgn_date']);
		$this->assign("end_date", $datapara['end_date']);
		$this->assign("status", $datapara['status']);
		$this->assign("querypara", $querypara);
		$this->assign("header_flag", 1);
		$this->display("secretlist");
	}

	/**举报的秘贴**/
	public function ReportSecretList(){

		$curr_page = I('page', 1, 'intval');
		$page_size = 20;

		$rrs = $this->secret->getSecretReport();

		$filter = array();
		$cmp_arr = array();

		if(!empty($rrs)){
			foreach($rrs as $val){
				array_push($filter, $val['secret_id']);	
			}
			$cmp_arr[$val['secret_id']] = $val['create_time'];
		}
		
		$params = array(
			'filter' => implode(",", $filter),
			'orderby' => ' ORDER BY status'	
		);

		$rs = $this->secret->getSecretList($params);
		
		$data = array();
		if(!empty($rs)){
			$limit = ($curr_page - 1)*$page_size;
			$data = array_slice($rs, $limit, $page_size);
		}

		if(!empty($data)){
			foreach($data as &$item){
				$item['create_time'] = $cmp_arr[$item['id']]? $cmp_arr[$item['id']] : $item['create_time'];
			}
		}
		
		$total = count($rrs);
		$total_num = ceil($total/$page_size);
		$this->assign("list", $data);
		$this->assign("total", $total);
		$this->assign("current", $curr_page);
		$this->assign("total_num", $total_num);
		$this->assign("index", 5);
		$this->display("secretreportlist");
	}
	
	/**举报秘贴作废**/
	public function ReportSecretDel(){
		
		$id = I('id', 0, 'intval');
		$status = I('status', 0, 'intval');

		$rs = $this->secret->statusSecret($id,$status);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'更改失败'), 'JSON');
		}else{
			
			$rrs = $this->secret->getReportSecretUserInfo($id);

			if(!empty($rrs)){
				foreach($rrs as $item){
					$mdata = array(
						'uid'		=> $item['user_id'],
						'direction'	=> 2,
						'tid'		=> 1111,	//客服ID
						'content'	=> '您举报的信息已被删除，感谢您的支持！',
						'status'	=> 1
					);
					insertByNoModel('t_user_chatmsg', '', 'DB_IMED', $mdata);
				}					
			}
			$this->ajaxReturn(array('code'=>1, 'message'=>'更改成功'), 'JSON');			
		}
	}

	/**删除秘密**/
	public function SecretDel(){
		
		$id = I('id', 0, 'intval');

		$rs = $this->secret->deleteSecret($id);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'删除失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'删除成功'), 'JSON');
		}
	}

    	/**更改状态**/
	public function SecretStatus(){
		
		$id = I('id', 0, 'intval');
		$status = I('status', 0, 'intval');

		$rs = $this->secret->statusSecret($id,$status);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'更改失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'更改成功'), 'JSON');
		}
	}

    
	/**秘密展示**/
	public function SecretShow(){
		
		$id = I('id', 0, 'intval');
		$rs = $this->secret->getSecretDetail($id);
		
		
		$this->display("secretdetail");
	}
    
   	/**搜索接口**/

	public function search($keyword,$bgn_date, $end_date, $status,$page=0)
	{
		$keyword=I('post.keyword','','string');
		$bgn_date=I('post.bgn_date','','string');
		$end_date=I('post.end_date','','string');
		$status=I('post.status',0,'int');
		$page = I('page',0,'int');
		
		if($keyword == '' && $bgn_date == '' && $end_date == '' && $status == 0)
			$this->ajaxReturn(array('code'=>-2, 'message'=>'无搜索条件', 'data'=>array()));
		$list = new ListModel();
		$re = $list->searchResult($keyword, $bgn_date, $end_date, $status,$page,$this->pagesize);
		if($re !== false){
			$counter = $list->searchResultCount($keyword, $bgn_date, $end_date, $status);
			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$re,'total'=>$counter,'pagesize'=>$this->pagesize));
		}			
		else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取搜索结果失败', 'data'=>array()));
		}
	}

	public function secretAdd(){
		$sec_theme = C('SECRET_THEME');
	
		$this->assign("sec_theme", $sec_theme);
		$this->display("secretadd");
	}

	public function secretPost(){
		//var_dump($_POST);exit;
		$data = array(
			'theme'			=> I('theme', 0, 'intval'),
			'type'			=> I('type', 0, 'intval'),
			'content'		=> I('content')
		);

		$data['user_id'] = C('SECRET_USR_ID');

		$rs = $this->secret->addSecret($data);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'添加失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'添加成功'), 'JSON');
		}
	}
	
	/**
	*秘贴评论
	*/
	public function secretComment(){
		
		$sid = I('sid', 0, 'intval');
		$currpage = I('page', 1 , 'intval');
		$page_size  = 20;
		
		$rs = $this->secret->getCommentList($sid, $currpage, $page_size);
		//var_dump($rs);
		if($rs === false){
			header("Content-Type: text/html;charset=utf-8");
			exit("系统错误");
		}else{
			
			$info_arr = $this->secret->getSecretInfo($sid);

			S(C('TOKEN_REDIS'));
			//echo "seccom_".$sid."_".$item['user_id'];exit;

			$default_uid_arr = C('SECRET_COMMENT_UID');
			
			if(!empty($rs)){
				foreach($rs as &$item){
					$item['nick_name'] = (S("seccom_".$sid."_".$item['user_id'])? S("seccom_".$sid."_".$item['user_id']).'楼' : '');
					$item['user_uid'] = in_array($item['user_id'],$default_uid_arr)? $item['user_id'].'|医道人员' : $item['user_uid'];
				}
			}

			$total = $this->secret->getCommentTotal($sid);
			$total_num = ceil($total/$page_size);
			
			$this->assign('sid', $sid);
			$this->assign('secret_content', (empty($info_arr)? '' : $info_arr[0]['content']));
			$this->assign('items', $rs);
			$this->assign("total", $total);
			$this->assign("current", $currpage);
			$this->assign("total_num", $total_num);
			//$this->assign("default_uid", $default_uid_arr);
			$this->display('commentlist');
		}
	}

	public function secretCommentPost(){
		$this->display("commentpost");
	}

	public function secretCommentAdd(){
		$sid = I("sid", 0, 'intval');
		$uid = I("uid", 0, 'intval');
		$content = I('content', '', 'strip_tags,addslashes');
		//var_dump($content);

		if(empty($sid) || empty($uid)){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'), 'JSON');
		}

		if((mb_strlen(trim($_POST['content']), 'utf-8') > 140) || (mb_strlen(trim($_POST['content']), 'utf-8') <=0)){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'评论字数1-140'),'JSON');
		}
		
		$data = array(
			'info_id'	=> $sid,
			'user_id'	=> $uid,
			'content'	=> replaceDirty($content),
			'type'		=> 2,
			'status'	=> 1
		);
		
		S(C('TOKEN_REDIS'));
		if(S("seccom_".$sid."_".$uid)){
			
		}else{
			$co_num = S("seccom_".$sid)? S("seccom_".$sid) : 1;
			S("seccom_".$sid."_".$uid, $co_num, 0);
			S("seccom_".$sid, ($co_num + 1), 0);
		}
		$floor = S("seccom_".$sid."_".$uid);

		$rs = insertByNoModel('t_info_comment', '', 'DB_IMED', $data);

		if($rs){
			$this->ajaxReturn(array('code'=>1, 'message'=>'发表成功'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发表失败'), 'JSON');
		}
	}
}