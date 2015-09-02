<?php
namespace Paper\Controller;
use Think\Controller;
class NewsController extends Controller {

	public function _initialize(){
		$this->news = D('News');
		$this->assign("img_domain", C('IMG_DOMAIN'));
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 2);
	}

	/**原文列表**/
    public function newsList(){

		$data = array();
		$str_url = ''; 

		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'search')){
			$rdata = array(
				'pub_date'	=> I('public_date', ''),
				'status'	=> I('status', '')
			);
			
			$str_url = "&action=search&public_date=".$rdata['pub_date']."&status=".$rdata['status'];
		
			$rs = $this->news->getSearchNews($rdata);
		}else{
			$rs = $this->news->getNewsList();
		}

		$zx_list = C('ZX_CATE_LIST');
		
		$curr_page = I('page', 1, 'intval');
		$page_size = 20;

		if(!empty($rs)){
			$limit = ($curr_page - 1)*$page_size;
			$data = array_slice($rs, $limit, $page_size);
		}
		
		if(!empty($data)){
			foreach($data as &$val){
				$val['category'] = $zx_list[$val['category']];
				$val['type'] = ($val['type'] == 1)? '新闻' : '资讯';
			}
		}
		
		//var_dump($data);exit;

		$total = count($rs);
		$total_num = ceil($total/$page_size);
		$this->assign("public_date", I('public_date', ''));
		$this->assign("status", I('status', ''));
		$this->assign("list", $data);
		$this->assign("total", $total);
		$this->assign("current", $curr_page);
		$this->assign("total_num", $total_num);
		$this->assign("str_url", $str_url);
		$this->display("news_list");
	}

	/**添加原文**/
    public function newsAdd(){
		$this->display("news");
	}

	/**类别初始化**/
	public function newsCateInit(){
		$zx_label = C('ZX_LABEL_LIST');
		$zx_relation = C('ZX_CATE_LABEL_LIST');
		$zx_category = C('ZX_CATE_LIST');

		$data = array();

		$data['category'] = $zx_category;

		if(!empty($zx_relation)){
			foreach($zx_relation as $key=>$val){
				$tmp_arr = explode(",", $val);
				foreach($tmp_arr as $item){
					$data['label'][$key][] = array(
						'id' => $item,
						'name' => $zx_label[$item]
					);
				}				
			}
		}

		$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$data), 'JSON');
	}

	/**查询关键字**/
	public function newsKeyWords(){
		$keywords = I('title');
		
		$sql = "SELECT id, key_word AS name FROM t_info_keys WHERE key_word LIKE '%".$keywords."%' GROUP BY key_word";
		$rs = queryByNoModel('t_info_keys', '', 'DB_STAT', $sql);
		
		if(empty($rs)){
			$this->ajaxReturn(array('code'=>1, 'data'=>array()), 'JSONP');
		}else{
			$this->ajaxReturn(array('code'=>1, 'data'=>$rs), 'JSONP');
		}
	}

	/**添加原文**/
	public function newsPost(){
		//var_dump($_POST);exit;
		$data = array(
			'title'			=> I('title', '', 'addslashes'),
			'sub_title'		=> I('sub_title', '', 'addslashes'),
			'type'			=> I('btype', 1, 'intval'),
			'category'		=> I('type', 0, 'intval'),
			'url'			=> I('url'),
			'keys'			=> I('keys'),
			'source'		=> I('source'),
			'level'			=> I('level', 1, 'intval'),
			'img_url'		=> I('img'),
			'content'		=> I('content', '', 'addslashes'),
			'action'		=> I('action', ''),
			'pub_date'		=> I('pub_date', date("Y-m-d")),
			'is_focus'		=> I('is_focus', 0, 'intval')
		);
		
		
		//var_dump($data);exit;

		if(!empty($data['action']) && ($data['action'] == 'edit')){
			
			$data['id'] = I('id', 0, 'intval');
				
			$rs = $this->news->updateNews($data);

			if($rs === false){
				$this->ajaxReturn(array('code'=>-1, 'message'=>'更新失败'), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>1, 'message'=>'更新成功'), 'JSON');
			}
		}else{
	
			$rs = $this->news->addNews($data);

			if($rs === false){
				$this->ajaxReturn(array('code'=>-1, 'message'=>'添加失败'), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>1, 'message'=>'添加成功'), 'JSON');
			}
		}
	}

	/**原文展示**/
	public function newsShow(){
		
		$id = I('id', 0, 'intval');
		$rs = $this->news->getNewsDetail($id);
		//var_dump($rs);exit;

		$content = str_replace("\n", "", $rs[0]['content']);
		$content = str_replace("\r", "", $rs[0]['content']);
		
		$this->assign('data', $rs[0]);
		$this->assign("content", $content);
		$this->display("news_edit");
	}

	/**原文审核**/
	public function newsChk(){
		
		$id = I('id', 0, 'intval');
		$rs = $this->news->checkNews($id);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'审核失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'审核成功'), 'JSON');
		}
	}
	
	/**删除原文**/
	public function newsDel(){
		
		$id = I('id', 0, 'intval');
		$rs = $this->news->deleteNews($id);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'删除失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'删除成功'), 'JSON');
		}
	}

	public function newsPub(){
		
		$id = I('id', 0, 'intval');
		$rrs = $this->news->publicNews($id);
		$rs = $this->news->getNewsDetail($id);
		
		
		$data = array(
			'type'		=> $rs[0]['type'],
			'category'	=> $rs[0]['category'],
			'title'		=> $rs[0]['title'],
			'source'	=> $rs[0]['source'],
			'pub_date'	=> $rs[0]['pub_date'],
			'src_url'	=> $rs[0]['src_url'],
			'src_type'	=> 1,
			'keys'		=> $rs[0]['keys'],
			'summary'	=> $rs[0]['sub_title'],
			'img_url'	=> $rs[0]['img_url'],
			'level'		=> $rs[0]['level'],
			'status'	=> 3
		);
		$srs = insertByNoModel('t_info_summary', '', 'DB_IMED', $data);	//插入资讯信息表
		if($srs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'), 'JSON');
		}else{
			$sdata = array(
				'info_id'	=> $srs,
				'content'	=> $rs[0]['content'],
				'status'	=> 3
			);

			$ers = insertByNoModel('t_info_entity', '', 'DB_IMED', $sdata);	//插入资讯实体表
		}
		//var_dump($ers);exit;

		//更新t_info_original表的idx
		$this->news->updateNewsIdx($id, $srs);

		if($ers === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'发布成功'), 'JSON');
		}
	}

	public function newsBack(){
		
		$id = I('id', 0, 'intval');

		$info = $this->news->getNewsDetail($id);
		

		if(empty($info)){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'该资讯没有详情内容'), 'JSON');
		}

		$idx = $info[0]['idx'];
		
		$update_sql = "UPDATE `t_info_summary` SET `update_time` = NOW(), `status` = 4 WHERE `info_id` = ".$idx;
		$rrs = updateByNoModel('t_info_summary', '', 'DB_IMED', $update_sql);

		if($rrs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'撤回失败'), 'JSON');
		}

		$rs = $this->news->backNews($id);
		
		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'撤回失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'撤回成功'), 'JSON');
		}
	}

	public function newsSearch(){
		
		$data = array(
			'pub_date'	=> I('public_date', ''),
			'status'	=> I('status', '')
		);
		
		$rs = $this->news->getSearchNews($data);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'系统错误'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$rs), 'JSON');
		}
	}

	public function newsPrev(){
	
		$id = I('id', 0, 'intval');
		$rs = $this->news->getNewsDetail($id);
		//var_dump($rs);

		if(empty($rs)){
			header("Content-Type: text/html;charset=utf-8");
			exit('页面不存在');
		}

		$arr_key = empty($rs[0]['keys'])? array() : (preg_split("/(，|,)/", trim($rs[0]['keys'])));

		$this->assign('title', $rs[0]['title']);
		$this->assign('pub_date', $rs[0]['pub_date']);
		$this->assign('keys', $arr_key);
		$this->assign('content', $rs[0]['content']);
		$this->assign('source', $rs[0]['source']);
		$this->assign('src_url', $rs[0]['src_url']);
		$this->assign('imed_url', C('ZX_DOMAIN').'/info/newsdetail/showdetail?id='.$rs[0]['idx']);

		$this->display('news_detail');
	}

	/**上传图片**/
	public function uploadImg(){
		//echo 'wwwww';exit;
		//var_dump($_FILES);exit;

		$tempFile = $_FILES['uploadify']['tmp_name'];
		$fileParts = pathinfo($_FILES['uploadify']['name']);
		$targetPath = C('IMG_PATH');
		$newPicName = 'ycl_'.uniqid();
		$targetName = $newPicName.'.'.$fileParts['extension'];
		$targetThumbName = $newPicName.'_thumb.'.$fileParts['extension'];
		$targetFile = rtrim($targetPath,'/').'/'.$targetName;
		$filesize = $_FILES['uploadify']['size'];
		
		/*if($filesize > 100*1024){
			echo "图片过大";
			
		}else{
			echo "图片正常";
		}*/

		// Validate the file type
		$fileTypes = array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'); // File extensions
		
		
		if (in_array($fileParts['extension'],$fileTypes)) {
			if(move_uploaded_file($tempFile,$targetFile)){

				$post_url = C('IMG_DOMAIN')."/admin/upload/infoimage";
				$post_field = 'uploadify';
				
				/***********缩略图************/
				getThumbPic($targetFile,240,240,'thumb');
				
				curlPost($post_url, $post_field, $targetName, $targetFile);

				$this->ajaxReturn(array('code'=>1, 'message'=>'上传成功', 'data'=>$targetThumbName), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>-1, 'message'=>'上传失败'), 'JSON');
			}
		} else {
			$this->ajaxReturn(array('code'=>-1, 'message'=>'上传图片格式不对'), 'JSON');
		}
	}

	public function uploadEditorImg(){
		$config = C('editor_config');

		if(isset($_REQUEST['action']) && ($_REQUEST['action']=='uploadimage')){
			//var_dump($_REQUEST);
			//var_dump($_FILES);exit;
			$tempFile = $_FILES['upfile']['tmp_name'];
			$fileParts = pathinfo($_FILES['upfile']['name']);
			$targetPath = C('IMG_PATH');
			$targetName = 'yc_'.uniqid().'.'.$fileParts['extension'];
			$targetFile = rtrim($targetPath,'/').'/'.$targetName;
			
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'); // File extensions
			
			
			if (in_array($fileParts['extension'],$fileTypes)) {
				if(move_uploaded_file($tempFile,$targetFile)){

					$post_url = C('IMG_DOMAIN')."/admin/upload/infoeditorimage";
					$post_field = 'upfile';
				
					curlPost($post_url, $post_field, $targetName, $targetFile);

					$result = array(
						'state' => 'SUCCESS',
						'url'	 => '/res/News/'.$targetName,
						'type'	 => $fileParts['extension'],
						'title'  => $targetName
					);
					$this->ajaxReturn($result, 'JSON');
				}else{
					$this->ajaxReturn(array('state'=>'FAIL'), 'JSON');
				}
			} else {
				$this->ajaxReturn(array('state'=>'FAIL', 'message'=>'上传图片格式不对'), 'JSON');
			}
		}else{
			$this->ajaxReturn($config, 'JSON');
		}	
	}
}