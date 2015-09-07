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
		
		$sql = "SELECT a.*, IFNULL(b.user_uid, '-') AS user_uid FROM t_info_comment a LEFT JOIN t_user_info b ON a.user_id=b.id WHERE a.type = 0 AND a.info_id = ".$nid." ORDER BY a.time DESC LIMIT ".$start.", ".$interval;
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
