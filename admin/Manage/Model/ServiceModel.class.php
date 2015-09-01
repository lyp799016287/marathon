<?php
namespace Manage\Model;
use Think\Model;

class ServiceModel extends Model {
	protected $connection = 'DB_ADMIN';
	protected $trueTableName = 't_service_chat';

	public function _initialize()
    {
        $this->admin_config = C('DB_ADMIN');
        $this->web_config = C('DB_IMED');
        // var_dump($stat_config);
    }

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
		$sql = "SELECT uid ,content,status,create_time,status FROM t_service_chat as chat  group by uid  ORDER BY create_time DESC ".$limits;

		$rs = $this->getRows($sql);
		return $rs;
	}

	/**
	*获取用户姓名信息
	*@author steptian
	*@return  false/array()
	*/
	public function getUserInfo($uid){
		
		//var_dump($limits);exit();
		$sql = "SELECT * FROM t_personal_info where user_id={$uid}";
		//var_dump($sql);exit();
		$rs = queryByNoModel('t_personal_info', '', $this->web_config,$sql);

		$rt = false;
		if($rs!==false){
			$rt = $rs[0];
		}
		return $rt;
	}

	/**
	*获取客服聊天列表总数
	*@author mandyzhou
	*@return  false/array()
	*/
	public function getChatListCount(){

		/*$sql = "SELECT id, title, url, CONCAT('focuslist/',img_url) AS imgurl, level, start_time, end_time, status FROM t_slider_panel_image WHERE status = 1 ORDER BY create_time DESC";*/
		$sql = "SELECT count(distinct uid) as counter FROM t_service_chat";
		$rs = $this->getRows($sql);

		$rt = false;
		if($rs!==false){
			$rt = $rs[0]['counter'];
		}
		return $rt;
	}


	/**
	*获取聊天详情
	*@author steptian
	*@param  $uid
	*@return false/array()
	*/
	public function getChatDetailInfo($uid){
		$sql = "SELECT *
			FROM t_service_chat WHERE uid={$uid} and status=0";
		
		$rs = $this->getRows($sql);
		if($rs!==false){
			return $rs;
		}else{
			return false;
		}
	}


	/**
	*	客服问题归类	type 类型,1、其他 2、资讯 3、同道 4、成长 5、科研 6、个人资料
	*/
	public function setQuestionClass($ids,$type){
		
		$sql = "update t_service_chat set type={$type} where chat_id in({$ids})";

		$rs = $this->exeSql($sql);
		if($rs!==false){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	合并线上用户咨询信息
	*/
	public function mergeInfo($tid){
        ## 确定需要插入的数据时间域
        $sql = "SELECT MAX(chat_id) chat_id FROM t_service_chat";
        $maxid_info = $this->getRows($sql);
        //$maxid_info = queryByNoModel('t_service_chat', '', $this->admin_config, $sql);
        $id_str = "";

        if($maxid_info === false)
            return array('code'=>-14, 'message'=>"查询错误：" . $sql);
        if(!is_null($maxid_info[0]['chat_id'])){
            $id_str = " id > " . $maxid_info[0]['chat_id'] ;
        }
        else{
        	$id_str = "id >0 ";
        }

        $tidlimit = '';
        if(!empty($tid)){
        	$tidlimit = " and tid={$tid}";
        }

        $sql = <<<EOF
        SELECT id as chat_id,tid as uid,content,create_time from t_user_chatmsg where 1=1 and uid=1111 {$tidlimit}  and  {$id_str} order by id desc;
EOF;
        //var_dump($sql);exit();
		$counter = 0;
		$info_daily  =queryByNoModel('t_user_chatmsg', '', $this->web_config,$sql);
        //$info_daily = $this->getRows($sql);
        if($info_daily === false)
            return array('code'=>-15, 'message'=>"查询错误：" . $sql);
        for($i = 0; $i < count($info_daily); $i++)
        {
            $insert_data = array();
            $insert_data['chat_id'] = $info_daily[$i]['chat_id'];
            $insert_data['uid'] = $info_daily[$i]['uid'];
            $insert_data['content'] = $info_daily[$i]['content'];
            $insert_data['create_time'] = $info_daily[$i]['create_time'];
            $insert_data['type'] = 0;
            $insert_data['status'] = 0;
            $insert_re = $this->add($insert_data); 
            //$insert_re = insertByNoModel('t_service_chat', '', $this->admin_config, $insert_data); 
            if($insert_re === false)
                return array('code'=>-16, 'message'=>"插入表数据错误：" . 't_info_daily');
            $counter++;
        }
        if($counter>0){
        	//本次同步过来的最大id
	        $max_chat_id = $info_daily[0]['chat_id'];
	        //本次同步过来的最小ID
	      	$min_chat_id = $info_daily[$counter-1]['chat_id'];
	        $obj_mod = M('t_user_chatmsg', '', $this->web_config);
	        $obj_mod->execute("SET NAMES utf8");

	        //置为已读状态
	        $where = "uid=1111 and id <={$max_chat_id} and id>={$min_chat_id}";
	      
        	$data['status'] = 2;
        	$data['modify_time'] = date("Y:m:d H:i:s");
        	$result = $obj_mod->where($where)->setField($data);
        }
        
        
        return array('code'=>1, 'message'=>"共同步 {$counter} 条数据");
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