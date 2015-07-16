<?php
namespace Manage\Controller;
use Think\Controller;
class FocusController extends Controller {

	protected $focus =  null;

	public function _initialize(){
		$this->focus = D('Focus');
		$this->assign("img_domain", C('IMG_DOMAIN'));
		$this->assign("menu_path", ROOT_PATH.'/admin_imed_me/');
		$this->assign("index", 3);
	}

	/**轮播列表**/
    public function focusList(){

		$curr_page = I('page', 1, 'intval');
		$page_size = 10;
		
		$rs = $this->focus->getFocusList();

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
		$this->assign("img_domain", C('IMG_DOMAIN'));
		$this->display("focuslist");
	}

	/**删除轮播**/
	public function focusDel(){
		
		$id = I('id', 0, 'intval');

		$rs = $this->focus->deleteFocus($id);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'删除失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'删除成功'), 'JSON');
		}
	}

	/**发布轮播**/
	public function focusPub(){
		
		$id = I('id', 0, 'intval');

		$rs = $this->focus->publicFocus($id);

		if($rs === false){
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'), 'JSON');
		}else{
			$this->ajaxReturn(array('code'=>1, 'message'=>'发布成功'), 'JSON');
		}
	}

	/**轮播展示**/
	public function focusShow(){
		
		$id = I('id', 0, 'intval');
		$rs = $this->focus->getFocusDetail($id);
		//var_dump($rs);exit;
		
		$this->assign('data', $rs[0]);
		$this->display("focusdetail");
	}

	/**添加轮播**/
	public function focusAdd(){
		$this->display("focusadd");
	}

	/**添加轮播**/
	public function focusPost(){
		//var_dump($_POST);exit;
		$data = array(
			'title'			=> I('title'),
			'url'			=> I('url'),
			'img_url'		=> I('img'),
			'start_date'	=> I('start_date'),
			'end_date'		=> I('end_date'),
			'level'			=> I('level', 1, 'intval'),
			'action'		=> I('action', ''),
			'type'			=> I('type', 1, 'intval')
		);
		
		if(ltrim($data['url'],'http://') == ''){
			$data['url'] = '';
		}

		if(empty($data['start_date'])){
			$data['start_time'] = strtotime("now");
		}else{
			$data['start_time'] = strtotime($data['start_date']);
		}

		if(empty($data['end_date'])){
			$data['end_time'] = strtotime("+7 days");
		}else{
			$data['end_time'] = strtotime($data['end_date']);
		}
		//var_dump($data);exit;

		if(!empty($data['action']) && ($data['action'] == 'edit')){
			
			$data['id'] = I('id', 0, 'intval');
				
			$rs = $this->focus->updateFocus($data);

			if($rs === false){
				$this->ajaxReturn(array('code'=>-1, 'message'=>'更新失败'), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>1, 'message'=>'更新成功'), 'JSON');
			}
		}else{
			$rs = $this->focus->addFocus($data);

			if($rs === false){
				$this->ajaxReturn(array('code'=>-1, 'message'=>'添加失败'), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>1, 'message'=>'添加成功'), 'JSON');
			}
		}
	}

	/**上传图片**/
	public function uploadImg(){
		//echo 'wwwww';exit;
		//var_dump($_FILES);exit;

		$tempFile = $_FILES['uploadify']['tmp_name'];
		$fileParts = pathinfo($_FILES['uploadify']['name']);
		$targetPath = C('IMG_PATH');
		$targetName = uniqid().'.'.$fileParts['extension'];
		$targetFile = rtrim($targetPath,'/').'/'.$targetName;
		
		// Validate the file type
		$fileTypes = array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'); // File extensions
		
		
		if (in_array($fileParts['extension'],$fileTypes)) {
			if(move_uploaded_file($tempFile,$targetFile)){
				
				$post_url = C('IMG_DOMAIN')."/admin/upload/focusimage";
				$post_field = 'uploadify';
				
				curlPost($post_url, $post_field, $targetName, $targetFile);

				$this->ajaxReturn(array('code'=>1, 'message'=>'上传成功', 'data'=>$targetName), 'JSON');
			}else{
				$this->ajaxReturn(array('code'=>-1, 'message'=>'上传失败'), 'JSON');
			}
		} else {
			$this->ajaxReturn(array('code'=>-1, 'message'=>'上传图片格式不对'), 'JSON');
		}
	}

}