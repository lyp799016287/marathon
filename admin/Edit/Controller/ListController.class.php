<?php
namespace Edit\Controller;
use Think\Controller;
use Edit\Model\ListModel as ListModel;

class ListController extends Controller {

	private $pagesize=20;

	## 获取来源信息
	public function getSource()
	{
		$list = new ListModel();
		$result = $list->getSrc();
		if($result !== false)
		{
			$re = array();
			for($i = 0; $i < count($result); $i++)
				$re[] = $result[$i]['source'];
			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$re));
		}
		else
			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取文章来源列表失败', 'data'=>array()));
	}

	## 全部文章的列表
	public function newsList($page = 0)
	{
		$list = new ListModel();
		$result = $list->getNewsList($page,$this->pagesize);
		if($result !== false){
			$counter = $list->getNewsListCount();

			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$result,'total'=>$counter,'pagesize'=>$this->pagesize));
		}
		else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取文章列表失败', 'data'=>array()));
		}

	}

	## 搜索接口
	public function search($keyword, $source, $bgn_date, $end_date, $status,$page=0)
	{
		$keyword=I('post.keyword','','string');
		$source=I('post.source','','string');
		$bgn_date=I('post.bgn_date','','string');
		$end_date=I('post.end_date','','string');
		$status=I('post.status',0,'int');
		$page = I('page',0,'int');
		
		if($keyword == '' && $source == '' && $bgn_date == '' && $end_date == '' && $status == 0)
			$this->ajaxReturn(array('code'=>-2, 'message'=>'无搜索条件', 'data'=>array()));
		$list = new ListModel();
		$re = $list->searchResult($keyword, $source, $bgn_date, $end_date, $status,$page,$this->pagesize);
		if($re !== false){
			$counter = $list->searchResultCount($keyword, $source, $bgn_date, $end_date, $status);
			$this->ajaxReturn(array('code'=>1, 'message'=>'', 'data'=>$re,'total'=>$counter,'pagesize'=>$this->pagesize));
		}			
		else{
			$this->ajaxReturn(array('code'=>-1, 'message'=>'获取搜索结果失败', 'data'=>array()));
		}
	}
	
}
