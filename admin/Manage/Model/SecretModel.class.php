<?php
namespace Manage\Model;
use Think\Model;

class SecretModel extends Model {
	protected $connection = 'DB_IMED';
	protected $trueTableName = 't_secret';

	/**
	*获取秘密列表
	*@author sheldonhuang
	*@return  false/array()
	*/
	public function getSecretList($data){
        $sqlpara="";
        if(!empty($data['keyword'])){
            $sqlpara=$sqlpara ." and a.content like '%".$data['keyword']."%'";
        }
        if(!empty($data['bgn_date'])){
            $sqlpara=$sqlpara ." and a.create_time >= '".$data['bgn_date']."'";
        }
        if(!empty($data['end_date'])){
            $sqlpara=$sqlpara ." and a.create_time <= '".$data['end_date']."'";
        }
        if($data['status']!=''){
            $sqlpara=$sqlpara ." and a.status = ".$data['status']."";
        }

		/*****modify by mandy****/
		if(isset($data['filter']) && !empty($data['filter'])){
			$sqlpara .= " AND a.id IN (".$data['filter'].")"; 
		}

		if(isset($data['except']) && !empty($data['except'])){
			$sqlpara .= " AND a.id NOT IN (".$data['except'].")";
		}

		/**用户信息（mobile）不显示**/
		/*$sql = "SELECT a.id,a.user_id,a.uptimes,a.type,a.status,a.content,a.create_time,b.user_uid as mobile,c.user_name as name
				FROM t_secret a LEFT JOIN t_user_info  b ON a.user_id=b.id  LEFT JOIN t_personal_info c ON a.user_id=c.user_id
				WHERE 1=1" . $sqlpara;*/
		$sql = "SELECT a.id,a.user_id,a.uptimes,a.type,a.status,a.content,a.create_time,c.user_name as name
				FROM t_secret a LEFT JOIN t_user_info  b ON a.user_id=b.id  LEFT JOIN t_personal_info c ON a.user_id=c.user_id
				WHERE 1=1" . $sqlpara;
		
		if(isset($data['orderby']) && !empty($data['orderby'])){
			$sql .= $data['orderby'];
		}else{
			$sql = $sql . " ORDER BY create_time DESC";
		}
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*获取被举报的秘密列表
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getSecretReport(){
		
		//$sql = "SELECT DISTINCT(secret_id) FROM t_secret_report";
		$sql = "SELECT secret_id, create_time FROM t_secret_report GROUP BY secret_id";	//获取被举报的秘贴ID及最开始的举报时间

		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*获取举报人的信息
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getReportSecretUserInfo($sid){
		
		$sql = "SELECT DISTINCT(user_id) FROM t_secret_report WHERE secret_id = ".$sid;

		$rs = $this->getRows($sql);
		return $rs;
	}

	
	/**
	*删除秘密
	*@author sheldonhuang
	*@param  $id
	*@return  false/true
	*/
	public function deleteSecret($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_secret SET status = 3 WHERE id = ".$id;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

    public function statusSecret($id,$status){
    
    if(empty($id)){
        return false;
    }

    $sql = "UPDATE t_secret SET status = ".$status." WHERE id = ".$id;

    $rs = $this->exeSql($sql, true);
    return $rs;
}

	
	/**
	*添加秘密
	*@author sheldonhuang
	*@modify by mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function addSecret($data){
		
		if(empty($data)){
			return false;
		}

		$sql = "INSERT INTO t_secret(user_id, type, status, content, theme_id)VALUES('".$data['user_id']."', '".$data['type']."', '1', '".$data['content']."', '".$data['theme']."')";

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*秘密详情
	*@author mandyzhou
	*@param $data
	*@return  false/array
	*/
	public function getSecretInfo($sid){
		if(empty($sid)){
			return false;
		}

		$sql = "SELECT * FROM t_secret WHERE id = ".$sid;
		
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*秘密的评论列表
	*@author mandyzhou
	*@param $sid, $currpage, $interval
	*@return  false/array
	*/
	public function getCommentList($sid, $currpage, $interval=10){
		
		if(empty($sid)){
			return false;
		}

		$start = ($currpage-1) * $interval;
		
		$sql = "SELECT a.*, IFNULL(b.user_uid, '-') AS user_uid FROM t_info_comment a LEFT JOIN t_user_info b ON a.user_id=b.id WHERE a.type = 2 AND a.info_id = ".$sid." ORDER BY a.time DESC LIMIT ".$start.", ".$interval;
		//echo $sql;
		//echo $sql;exit;
		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*秘密的评论总数
	*@author mandyzhou
	*@param $sid
	*@return  int
	*/
	public function getCommentTotal($sid){
		
		if(empty($sid)){
			return 0;
		}

		$sql = "SELECT COUNT(*) AS total FROM t_info_comment WHERE type =2 AND info_id =".$sid;
		
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