<?php
namespace Edit\Controller;
use Think\Controller;
use Edit\Model\DetailModel as DetailModel;
use Edit\Model\ImedModel as ImedModel;

class DetailController extends Controller {
	private $status2 = 2; ## 编辑未发布
	private $status3 = 3; ## 发布
	private $status4 = 4; ## 删除
	private $status5 = 5; ## 发布后删除


	## 获取文章具体内容
	public function getDetail($id)
	{
		$list = new DetailModel();
		$result = $list->detailInfo($id);
		if($result !== false){
			$result = $result[0];
			$result['title'] = urldecode($result['title']);
			$result['summary'] = urldecode($result['summary']) ;
			$result['content'] = urldecode($result['content']) ;
			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$result));
		}			
		else{

			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取文章来源列表失败', 'data'=>array()));
		}
	}

	## “保存”按钮点击
	public function saveNews($id, $title, $summary, $content)
	{
		$id=I('post.id',0,'int');
		$title = I('post.title','','addslashes');
		$summary = I('post.summary','','addslashes');
		$content = I('post.content','','addslashes');
		//var_dump($content);exit();
		$save = new DetailModel();
		
		$re = $save->updateInfo($id, $title, $summary, $content, $this->status2);
		if($re!==false)
			$this->ajaxReturn(array('code'=>1, 'message'=>'保存成功'));
		else
			$this->ajaxReturn(array('code'=>-1, 'message'=>'保存失败'));
	}

	## “发布”按钮点击
	## 参数变为$id, $title, $summary, $content
	public function pubNews($id, $title, $summary, $content)
	{
		$id=I('post.id',0,'int');
		$title = I('post.title','','string');
		$summary = I('post.summary','','string');
		$content = I('post.content','','string');

		$raw = new DetailModel();
		$reRaw = $raw->updateInfo($id, $title, $summary, $content, $this->status3);
		if($reRaw === false)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'));
		$result = $raw->detailInfo($id);
		//var_dump($result);exit();
		if(!$result)
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'));
		$source = $result[0]['source'];
		$pub_date = $result[0]['pub_date'];
		$url = $result[0]['url'];
		$author = $result[0]['author'];
		$imgTag = $result[0]['imgtag'];

		## 写入到正式环境的数据库中
		$pub = new ImedModel();
		$rePub = $pub->pubInfo($title, $source, $pub_date, $url, $summary, $author, $content, $imgTag);
		
		if($rePub!==false)
			$this->ajaxReturn(array('code'=>1, 'message'=>'发布成功'));
		else
			$this->ajaxReturn(array('code'=>-1, 'message'=>'发布失败'));
	}

	## “删除”按钮点击
	public function delNews($id)
	{
		$del = new DetailModel();
		$re = $del->delStatus(intval($id));
		if($re)
			$this->ajaxReturn(array('code'=>1, 'message'=>'删除成功'));
		else
			$this->ajaxReturn(array('code'=>-1, 'message'=>'删除失败'));
	}
}
