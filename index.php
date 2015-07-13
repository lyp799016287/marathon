<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

define('ENV_CONFIG', 'dev');	//正式环境为product

if(ENV_CONFIG=='product'){
	define('ROOT_PATH', '/data');
}else{
	define('ROOT_PATH', '/yzserver/www');
}

// 定义应用目录
define('APP_PATH',ROOT_PATH.'/admin_imed_me/admin/');

define('THINK_LIB_PATH', ROOT_PATH.'/ThinkPHPLIB/');

/*************记录访问日志 Testing Start****************/
//var_dump($_SERVER);var_dump($_REQUEST);exit;
session_start();
$strLog = '';
foreach($_REQUEST as $key=> $val){
	$strLog .= $key.":".$val.";";
}
$message = date("Y-m-d H:i:s")."\t".str_replace('/index.php','',$_SERVER['DOCUMENT_URI'])."\t".rtrim($strLog,";")."\t".$_REQUEST['uid']."\t".$_REQUEST['device_token']."\t".$_REQUEST['timestamp']."\t";
$message .= (isset($_SESSION['user_id'])? $_SESSION['user_id'] : '')."\t";
$message .= (isset($_SESSION['user_uid'])? $_SESSION['user_uid'] : '')."\t";
$message .= time()."\r\n";

file_put_contents(APP_PATH.'Runtime/StatLogs/'.date("y-m-d").".txt",$message, FILE_APPEND);
/*************记录访问日志 Testing End****************/

// 引入ThinkPHP入口文件
require THINK_LIB_PATH.'ThinkPHP.php';
//添加M登录态检查验证List
$H5CheckList = C('H5CHECKLIST');
if(!empty($H5CheckList)&&is_array($H5CheckList)){
	//var_dump($H5CheckList);
	for($i=0;$i<count($H5CheckList);$i++){
		
		if(stristr($_SERVER['REQUEST_URI'],$H5CheckList[$i])!=false){
			//var_dump($_SERVER['REQUEST_URI']);
			//var_dump(import('Info/Controller','','/NewsdetailController.class.php')); 
			//echo '-----------';
			/*
			$login = new Login\Controller\LoginController();
			//var_dump($login);
			
			$loginResult = $login->verifyByH5();
			if($loginResult['code']!=1){
				redirect('/');
			}
			break;
			*/
		}
	}
	
}
