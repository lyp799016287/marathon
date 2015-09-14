<?php
namespace Paper\Controller;
use Think\Controller;
class NewsController extends Controller {

	public function _initialize(){
		$this->news = D('News');
		$this->comment = D('Comment');
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
		
		$zx_list = C('ZX_LABEL_LIST');
		$zx_category = C('ZX_CATE_LIST');
		
		$curr_page = I('page', 1, 'intval');
		$page_size = 20;

		if(!empty($rs)){
			$limit = ($curr_page - 1)*$page_size;
			$data = array_slice($rs, $limit, $page_size);
		}
		
		if(!empty($data)){
			foreach($data as &$val){
				$val['category'] = $zx_list[$val['category']];
				$val['type'] = $zx_category[$val['type']];
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
			'type'			=> I('type', 1, 'intval'),			//分类
			'category'		=> I('btype', 0, 'intval'),			//标签
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
		
		if(empty(trim($rs[0]['keys']))){
			$keys_arr = array();
		}else{
			$keys_arr = explode(",", trim($rs[0]['keys']));
		}
		
		$this->assign('data', $rs[0]);
		$this->assign("content", $content);
		$this->assign("keys_arr", $keys_arr);
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

	/**原文发布**/
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
			'is_focus'	=> $rs[0]['is_focus'],
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


			//插入Stat关键字表
			if(!empty($rs[0]['keys'])){
				$tmp_keys_arr = explode(",", $rs[0]['keys']);
				
				if(!empty($tmp_keys_arr)){
					foreach($tmp_keys_arr as $item){
						$kdata = array(
							'info_id'	=>$srs,
							'key_word'	=>$item
						);
						$krs = insertByNoModel('t_info_keys', '', 'DB_STAT', $kdata);	//插入资讯信息表		
					}
				}
			}	
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

	/**原文撤回**/
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

	/**原文搜索**/
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

	/**原文预览**/
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

	/**资讯评论**/
	public function newsCommentList(){

		$id = I('id', 0, 'intval');		//此处id为外网对应的资讯id(t_info_summary-info_id)

		$currpage = I('page', 1 , 'intval');
		$page_size  = 20;

		$str_url = '';
		$rdata = array();

		$rdata['id'] = $id;

		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'search')){
			$rdata['pub_date'] = I('public_date', '');
			$rdata['is_top'] = I('status', '');
			$str_url = "&action=search&public_date=".$rdata['pub_date']."&status=".$rdata['is_top'];
		}
		
		$rs = $this->comment->getInfoComments($rdata, $currpage, $page_size);
		
		if($rs === false){
			header("Content-Type: text/html;charset=utf-8");
			exit("查询有误");
		}else{
			
			$total = $this->comment->getInfoCommentTotal($rdata);
			$total_num = ceil($total/$page_size);
			
			$this->assign("public_date", I('public_date', ''));
			$this->assign("status", I('status', ''));
			$this->assign('nid', $id);
			$this->assign('items', $rs);
			$this->assign("total", $total);
			$this->assign("current", $currpage);
			$this->assign("total_num", $total_num);
			//$this->assign("default_uid", $default_uid_arr);
			$this->assign("str_url", $str_url);
			$this->display('comment_list');
		}
	}

	/**资讯评论操作**/
	public function newsCommentModify(){
		
		$id = I('id', 0, 'intval');		 //comment_id
		$type = I('type', 0, 'intval');

		/**
		*$type: 1 删除; 2 撤回删除; 3 加精; 4 取消加精
		**/
		$rs = $this->comment->modifyComment($id, $type);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'操作失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'操作成功'), 'JSON');
		}
	}

	/**发表资讯评论**/
	public function newsCommentPost(){
		
		$id = I('nid', 0, 'intval');	//此处nid为外网对应的资讯id(t_info_summary-info_id)
		
		//筛选前台运营账号
		$sql = 'SELECT a.id, b.user_name FROM t_user_info a LEFT JOIN t_personal_info b ON a.id = b.user_id  WHERE a.user_uid > 20000000000 AND a.user_uid < 20000000021';
		$rs = queryByNoModel('t_user_info', '', 'DB_IMED', $sql);
		
		$this->assign("users", $rs);
		$this->display('comment_add');
	}

	/**发表资讯评论**/
	public function newsCommentAdd(){
		
		$nid = I('nid', 0, 'intval');
		$uid = I('uid', 0, 'intval');
		
		$content = I('content', '', 'strip_tags,addslashes');
		//var_dump($content);

		if(empty($nid) || empty($uid)){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'参数错误'), 'JSON');
		}

		if((mb_strlen(trim($_POST['content']), 'utf-8') > 140) || (mb_strlen(trim($_POST['content']), 'utf-8') <=0)){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'评论字数1-140'),'JSON');
		}
		
		$data = array(
			'info_id'	=> $nid,
			'user_id'	=> $uid,
			'content'	=> replaceDirty($content),
			'type'		=> 0,
			'status'	=> 1
		);
		
		$rs = insertByNoModel('t_info_comment', '', 'DB_IMED', $data);

		if($rs){
			$this->ajaxReturn(array('code'=>1, 'message'=>'发表成功'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发表失败'), 'JSON');
		}
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