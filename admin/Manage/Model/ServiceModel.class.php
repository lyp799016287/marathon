<?php
namespace Manage\Model;
use Think\Model;

class ServiceModel extends Model {
	protected $connection = 'DB_IMED';
	protected $trueTableName = 't_user_chatmsg';

	/**
	*获取客服聊天列表
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getChatList($page, $pagesize){
		$limits = '';
		if(!empty($pagesize)){
			if(empty($page)){
				$page = 0;
			}
			$limits = "limit ".$page*$pagesize.",".$pagesize;
		}
		//var_dump($limits);exit();
		$sql = "SELECT chat.tid,person.user_name,chat.content,chat.status,chat.create_time FROM t_user_chatmsg as chat left join t_personal_info as person on chat.tid=person.user_id WHERE uid=1111 and direction=2 and status!=2 and status!=3 group by chat.tid  ORDER BY create_time DESC ".$limits;

		$rs = $this->getRows($sql);
		return $rs;
	}


	/**
	*获取客服聊天列表总数
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getChatListCount(){

		/*$sql = "SELECT id, title, url, CONCAT('focuslist/',img_url) AS imgurl, level, start_time, end_time, status FROM t_slider_panel_image WHERE status = 1 ORDER BY create_time DESC";*/
		$sql = "SELECT count(*) as counter FROM t_user_chatmsg WHERE uid=1111 and direction=2 and status!=2 and status!=3 group by tid";
		$rs = $this->getRows($sql);
		$rt = false;
		if($rs!==false){
			$rt = $rs[0]['counter'];
		}
		return $rt;
	}

	
	

	/**
	*获取轮播图详情
	*@author mandyzhou
	*@param  $id
	*@return false/array()
	*/
	public function getChatDetail($id){
		$sql = "SELECT id,  
			title, 
			url, 
			img_url, 
			level, 
			start_time, 
			end_time, 
			status
			FROM t_slider_panel_image WHERE id=".$id;
		$rs = $this->getRows($sql);
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