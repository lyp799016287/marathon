<?php
namespace Manage\Model;
use Think\Model;

class FocusModel extends Model {
	protected $connection = 'DB_IMED';
	protected $trueTableName = 't_slider_panel_image';

	/**
	*获取轮播图列表
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getFocusList($page, $interval){

		/*$sql = "SELECT id, title, url, CONCAT('focuslist/',img_url) AS imgurl, level, start_time, end_time, status FROM t_slider_panel_image WHERE status = 1 ORDER BY create_time DESC";*/
		$sql = "SELECT id, title, url, CONCAT('focuslist/',img_url) AS imgurl, level, start_time, end_time, status FROM t_slider_panel_image WHERE status !=2 ORDER BY create_time DESC";
		$rs = $this->getRows($sql);
		return $rs;
	}

	
	/**
	*删除轮播图
	*@author mandyzhou
	*@param  $id
	*@return  false/true
	*/
	public function deleteFocus($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_slider_panel_image SET status = 2 WHERE id = ".$id;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	public function publicFocus($id){
		
		if(empty($id)){
			return false;
		}

		$sql = "UPDATE t_slider_panel_image SET status = 1 WHERE id = ".$id;

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*获取轮播图详情
	*@author mandyzhou
	*@param  $id
	*@return false/array()
	*/
	public function getFocusDetail($id){
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

	/**
	*添加轮播图
	*@author mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function addFocus($data){
		
		if(empty($data)){
			return false;
		}

		//$sql = "INSERT INTO t_slider_panel_image (title,url,img_url,level,start_time,end_time,status)VALUES('".$data['title']."', '".$data['url']."', '".$data['img_url']."', '".$data['level']."', '".$data['start_time']."', '".$data['end_time']."', '".$data['status']."')";

		$sql = "INSERT INTO t_slider_panel_image (title,type,url,img_url,level,start_time,end_time,status)VALUES('".$data['title']."', '".$data['type']."', '".$data['url']."', '".$data['img_url']."', '".$data['level']."', '".$data['start_time']."', '".$data['end_time']."', '1')";

		$rs = $this->exeSql($sql, true);
		return $rs;
	}

	/**
	*更新轮播图信息
	*@author mandyzhou
	*@param $data
	*@return  false/true
	*/
	public function updateFocus($data){
		
		if(empty($data)){
			return false;
		}

		$sql = "UPDATE t_slider_panel_image SET title = '".$data['title']."', type = '".$data['type']."', url = '".$data['url']."', img_url = '".$data['img_url']."', level = '".$data['level']."', start_time = '".$data['start_time']."', end_time = '".$data['end_time']."', modify_time = NOW() WHERE id = ".$data['id'];

		//echo $sql;exit;

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