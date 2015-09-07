<?php
namespace Paper\Model;
use Think\Model;

class NewsModel extends Model {
	protected $connection = 'DB_ADMIN';
	protected $trueTableName = 't_info_original';

	/**
	*获取原创文章列表
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getNewsList(){

		$sql = "SELECT id, type, category, title, source, status, level, pub_date, idx, is_focus FROM t_info_original WHERE status !=4 ORDER BY pub_date DESC, level DESC";
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*搜索原创文章列表
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getSearchNews($data){
		
		$condition = '';
		if(!empty($data['pub_date'])){
			$condition .= " AND pub_date = '".$data['pub_date']."'";
		}

		if($data['status'] != ''){
			$condition .= " AND status = ".$data['status'];
		}else{
			$condition .= " AND status !=4";
		}

		$sql = "SELECT id, type, category, title, source, status, level, pub_date, idx, is_focus FROM t_info_original WHERE 1=1 ".$condition." ORDER BY pub_date DESC, level DESC";
		//echo $sql;exit;
		$rs = $this->getRows($sql);
		return $rs;
	}

	
	/**
	*删除原文
	*@author mandyzhou
	*@param  $id
	*@return  false/true
	*/
	public function deleteNews($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_info_original SET status = 4 WHERE id = ".$id;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*发布原文
	*@author mandyzhou
	*@param  $id
	*@return  false/true
	*/
	public function publicNews($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_info_original SET status = 3 WHERE id = ".$id;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}
	
	/**
	*审核原文
	*@author mandyzhou
	*@param  $id
	*@return  false/true
	*/
	public function checkNews($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_info_original SET status = 1 WHERE id = ".$id;

		$rs = $this->exeSql($sql);
		return $rs;
	}

	/**
	*撤回原文
	*@author mandyzhou
	*@param  $id
	*@return  false/true
	*/
	public function backNews($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_info_original SET status = 0 WHERE id = ".$id;

		$rs = $this->exeSql($sql);
		return $rs;
	}

	/**
	*获取资讯详情
	*@author mandyzhou
	*@param  $id
	*@return false/array()
	*/
	public function getNewsDetail($id){
		$sql = "SELECT `id`,
				`type`,
				`category`, 
				`title`, 
				`sub_title`,
				`pub_date`,
				`source`, 
				`src_url`, 
				`keys`, 
				`content`, 
				`img_url`, 
				`level`,
				`create_time`,
				`idx`,
				`is_focus`
			FROM t_info_original WHERE id=".$id;	//echo $sql;exit;
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*添加
	*@author mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function addNews($data){
		
		if(empty($data)){
			return false;
		}
		
		$sql = 'INSERT INTO t_info_original(`type`, `category`, `title`, `pub_date`, `sub_title`, `source`, `src_url`, `keys`, `content`, `img_url`, `status`, `level`, `is_focus`)VALUES("'.$data['type'].'", "'.$data['category'].'", "'.$data['title'].'","'.$data['pub_date'].'", "'.$data['sub_title'].'",  "'.$data['source'].'", "'.$data['url'].'", "'.$data['keys'].'", "'.$data['content'].'", "'.$data['img_url'].'", 0, '.$data['level'].', '.$data['is_focus'].')';
		//echo $sql;exit;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*更新资讯信息
	*@author mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function updateNews($data){
		
		if(empty($data)){
			return false;
		}

		$sql = 'UPDATE t_info_original SET `type` = "'.$data['type'].'", `category` = "'.$data['category'].'",`title` = "'.$data['title'].'" ,`sub_title` = "'.$data['sub_title'].'" ,`source` = "'.$data['source'].'" ,`src_url` = "'.$data['url'].'" ,`keys` = "'.$data['keys'].'" ,`content` = "'.$data['content'].'" ,`img_url` = "'.$data['img_url'].'" ,`update_time` = NOW() ,`level` = "'.$data['level'].'", `pub_date` = "'.$data['pub_date'].'", `is_focus` = '.$data['is_focus'].' WHERE id = '.$data['id'];

		//echo $sql;exit;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*更新资讯关联ID
	*@author mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function updateNewsIdx($id, $idx){
		
		if(empty($id) || empty($idx)){
			return false;
		}

		$sql = "UPDATE `t_info_original` SET `idx` = ".$idx." WHERE `id` = ".$id;
		
		$rs = $this->exeSql($sql, true);
		return $rs;
	}

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