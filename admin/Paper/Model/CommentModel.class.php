<?php
namespace Paper\Model;
use Think\Model;

class CommentModel extends Model {
	protected $connection = 'DB_IMED';
	protected $trueTableName = 't_info_comment';

	/**
	*获取资讯的评论
	*@author mandyzhou
	*@param $nid	资讯ID
	*@return  false/array
	*/
	public function getInfoComments($nid, $currpage, $interval=10){
		
		if(empty($nid)){
			return false;
		}

		$start = ($currpage-1) * $interval;
		
		$sql = "SELECT a.*, IFNULL(b.nick_name, '-') AS nick_name FROM t_info_comment a LEFT JOIN t_personal_info b ON a.user_id=b.user_id WHERE a.type = 0 AND a.info_id = ".$nid." ORDER BY a.time DESC LIMIT ".$start.", ".$interval;
		//echo $sql;exit;
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*获取资讯的评论总数
	*@author mandyzhou
	*@param $nid	资讯ID
	*@return  int
	*/
	public function getInfoCommentTotal($nid){
		
		if(empty($nid)){
			return 0;
		}

		$sql = "SELECT COUNT(*) AS total FROM t_info_comment WHERE type =0 AND info_id =".$nid;
		
		$rs = $this->getRows($sql);
		if(!$rs){
			return 0;
		}else{
			return $rs[0]['total'];
		}
	}

	/**
	*评论操作函数(删除、撤回、加精等)
	*@author mandyzhou
	*@param $id	评论ID
	*@param $type 操作类型
	*@return  int
	*/
	public function modifyComment($id, $type){
		
		if(empty($id) || empty($type)){
			return false;
		}

		switch($type){
			case 1:		//删除
				$sql = "UPDATE t_info_comment SET status = 2 WHERE comment_id = ".$id;
				break;
			case 2:		//撤回删除
				$sql = "UPDATE t_info_comment SET status = 1 WHERE comment_id = ".$id;
				break;
			case 3:		//加为精品评论
				$sql = "UPDATE t_info_comment SET is_top = 1 WHERE comment_id = ".$id;
				break;
			case 4:		//撤销精品评论
				$sql = "UPDATE t_info_comment SET is_top = 0 WHERE comment_id = ".$id;
				break;
		}

		$rs = $this->exeSql($sql);
		return $rs;
	}

	private function getRows($sql){

		$this->execute("SET NAMES utf8");

		$rs = $this->query($sql);
		return $rs;
	}

	private function exeSql($sql,$parse=false){
		
		$this->execute("SET NAMES utf8");

		$rs = $this->execute($sql,$parse=false);
		return $rs;
	}
}
