<?php
namespace Manage\Controller;
use Think\Controller;
class SecretController extends Controller {

	protected $secret =  null;

	public function _initialize(){
		$this->secret = D('Secret');
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 4);
	}

	/**秘密列表**/
    public function SecretList(){
        $querypara='';
		$curr_page = I('page', 1, 'intval');
		$page_size = 20;
        
		$datapara = array(
			'keyword'		=> I('keyword'),
			'bgn_date'		=> I('bgn_date','','string'),
			'end_date'		=> I('end_date','','string'),
			'status'    	=> I('status','','string'),

		);
        foreach($datapara as $key => $val){
            $querypara =$querypara.'&'.$key.'='.$val;
        }        
		//echo var_dump($data);        
        
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
		$this->display("secretlist");
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
	
}