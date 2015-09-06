<?php
namespace Shell\Controller;
use Think\Controller;

class DataController extends Controller {
	
	public function addNewUsers(){
		
		$com_users = C('COMMENT_USERS');

		//var_dump($com_users);

		if(!empty($com_users)){
			foreach($com_users as $key=>$user){
				$num = $key + 1;
				$data = array(
					'user_uid'	=> '200000000'.($num <10? '0'.$num : $num),
					'password'	=> '8:4777',
					'user_from'	=> 1,	//后台自建用户
					'status'	=> 1
				);
				//var_dump($data);
				//$urs = insertByNoModel('t_user_info', '', 'DB_IMED', $data);	//插入用户表

				$pdata = array(
					'user_id'	=> $urs,
					'user_name'	=> $user,
					'nick_name'	=> $user,
				);

				//$prs = insertByNoModel('t_personal_info', '', 'DB_IMED', $pdata);	//插入用户详情表
			}
		}
	}
}