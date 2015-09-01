<?php
namespace Manage\Controller;
use Think\Controller;
class ServiceController extends Controller {

	

	public function _initialize(){
		$this->service = D('Service');
		$this->pagesize = 15;
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 6);
		$this->assign('env','step.');

	}

	/**秘密列表**/
    public function ChatList(){



        $querypara='';
		$curr_page = I('page', 1, 'intval');
		
        
		$datapara = array(
			'keyword'		=> I('keyword'),
			'bgn_date'		=> I('bgn_date', ''),
			'end_date'		=> I('end_date', ''),
			'status'    	=> I('status',1,'intval'),
			
		);
        foreach($datapara as $key => $val){
            $querypara =$querypara.'&'.$key.'='.$val;
        }   

		
		$this->display("chatlist");
	}

	/**获取聊天列表**/
    public function getChatList(){
    	$page = I('page',1,'intval');

    	
        $data = $this->service->getChatList($page,$this->pagesize);
        $counter = $this->service->getChatListCount();
        if($data !== false){
			$this->ajaxReturn(array('code'=>1, 'message'=>'获取搜索结果成功', 'data'=>$data,'total'=>$counter,'pagesize'=>$this->pagesize));
		}			
		else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取搜索结果失败', 'data'=>array()));
		}
	}


	/**秘密列表**/
    public function ChatDetail(){

        $querypara='';

		$token = $this->getChatToken();
		//var_dump($token);exit();
		$this->assign('token',$token);
		$this->assign('uid',1111);
		$tid = I('tid',0,'intval');
       	$user_name = $this->getUserName($tid);
		$this->assign('user_name',$user_name);
		$this->display("chatdetail");
	}

	/**秘密列表**/
    public function getChatDetailInfo(){

        $querypara='';
		$tid = I('tid',0,'intval');
		$result = $this->service->getChatDetailInfo($tid);
		if($result===false){
			$this->ajaxReturn(array("code"=>-1,'message'=>'获取数据失败'));
		}else{
			$this->ajaxReturn(array("code"=>1,'message'=>'获取数据成功','data'=>$result));
			
		}
		
	}

	private function getUserName($uid){
		$user_name = '';
		$result = $this->service->getUserInfo($uid);
    	if(!empty($result)){
    		
    		$user_name = $result['user_name'];
    	}
    	return $user_name;
	}

	/**客服自动登录,返回token**/
    private function getChatToken(){
    	//$mobile, $password, $timestamp, $device_token
    	$postFields = array(
    		'mobile'=>'11111111111',
    		'password'=>'8:4yzb',
    		'timestamp'=>time(),
    		'device_token'=>'admin'
    	);
    	$token = '';
    	$result = $this->curl('https://dev.m.imed.me/login/login/varify',$postFields);
    	if(!empty($result)){
    		$tokenjson = json_decode($result);
    		//var_dump($tokenjson);exit();
    		$token = $tokenjson->data->token;
    	}
    	return $token;
	}

	/*
	客服问题归类	type 类型,1、其他 2、资讯 3、同道 4、成长 5、科研 6、个人资料
	*/
	public function classify(){
		$ids = I('ids','');
		$type = I('type',1,'intval');
		$idArray = array();
		//检查并处理ids合法性
		if(!empty($ids)){
			$idArray = explode(',', $ids);
			for($i=0;$i<count($idArray);$i++){
				$idArray[$i]=intval($idArray[$i]);
			}
		}
		$ids = implode(',', $idArray);
		if(count($idArray)>0){
			$ret = $this->service->setQuestionClass($ids,$type);
			if($ret!=false){
				$this->ajaxReturn(array("code"=>1,'message'=>'设置分类成功'));
			}
		}else{
			$this->ajaxReturn(array("code"=>-1,'message'=>'设置分类失败'));	
		}

		$this->ajaxReturn(array("code"=>-2,'message'=>'设置分类失败，缺少ids'));

	}

	public function mergeInfo(){
		$tid = I('tid',0,'intval');
		if($tid==0){
			$tid=null;
		}
		$this->ajaxReturn($this->service->mergeInfo($tid));
	}


	protected function curl($url, $postFields = null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.1)');
        curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器


		//curl_setopt($ch,CURLOPT_HTTPHEADER,array("Expect:"));
		if (is_array($postFields) && 0 < count($postFields))
		{
		$postBodyString = "";
		$postMultipart = false;
		foreach ($postFields as $k => $v)
		{
		if("@" != substr($v, 0, 1))//判断是不是文件上传
		{
		$postBodyString .= "$k=" . urlencode($v) . "&";
		}
		else//文件上传用multipart/form-data，否则用www-form-urlencoded
		{
		$postMultipart = true;
		}
		}
		unset($k, $v);
		curl_setopt($ch, CURLOPT_POST, 1);
		if ($postMultipart)
		{
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}
		else
		{
		    //var_dump($postBodyString);
		curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
		}
		}
		$reponse = curl_exec($ch);
		        //return curl_getinfo($ch);
		if (curl_errno($ch))
		{
		throw new Exception(curl_error($ch),0);
		}
		else
		{
		$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (200 !== $httpStatusCode)
		{
		//throw new Exception($reponse,$httpStatusCode);
		}
		}
		curl_close($ch);
		return $reponse;
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

	
	
}