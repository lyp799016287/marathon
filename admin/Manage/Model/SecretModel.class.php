<?php
namespace Manage\Model;
use Think\Model;

class SecretModel extends Model {
	protected $connection = 'DB_IMED';
	protected $trueTableName = 't_slider_panel_image';

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

		/*$sql = "SELECT a.id,a.user_id,a.uptimes,a.type,a.status,a.content,a.create_time,b.user_uid as mobile,c.user_name as name
				FROM t_secret a LEFT JOIN t_user_info  b ON a.user_id=b.id  LEFT JOIN t_personal_info c ON a.user_id=c.user_id
				WHERE 1=1" . $sqlpara;
		*/
		$sql = "SELECT a.id,a.user_id,a.uptimes,a.type,a.status,a.content,a.create_time,b.user_uid AS mobile,c.user_name AS NAME,IFNULL(d.`secret_id`, 0) AS sid
				FROM t_secret a LEFT JOIN t_secret_report d ON a.id = d.`secret_id` LEFT JOIN t_user_info  b ON a.user_id=b.id LEFT JOIN t_personal_info c ON a.user_id=c.user_id
				WHERE 1=1" . $sqlpara;
        $sql = $sql . " ORDER BY create_time DESC";
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