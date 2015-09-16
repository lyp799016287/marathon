<?php
$config_all = array();
if(ENV_CONFIG=='product'){
	$config_all = array(
	//'配置项'=>'配置值'
	'DEFAULT_MODULE'        =>  'Home',
	/* 模板引擎设置 */
    'TMPL_TEMPLATE_SUFFIX'  =>  '.htm',     // 默认模板文件后缀
    // 布局设置
    'TMPL_ENGINE_TYPE'      =>  'Smarty',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    'TMPL_L_DELIM'          =>  '{',        // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}',          // 模板引擎普通标签结束标记
	
	'LOAD_EXT_CONFIG'		=> 'db,content,redis,privilege',
	//返回压缩设置
	'GZIP_CONFIG' =>'OFF',
	'GZIP_PREFIX' =>'iknowdoctor.com',	//gzip压缩时的前缀，15位
	'H5CHECKLIST' => array(
		'/info/NewsDetail/showDetail',
	));
}else{
	$config_all = array(
	//'配置项'=>'配置值'
	'DEFAULT_MODULE'        =>  'Home',
	/* 模板引擎设置 */
    'TMPL_TEMPLATE_SUFFIX'  =>  '.htm',     // 默认模板文件后缀
    // 布局设置
    'TMPL_ENGINE_TYPE'      =>  'Smarty',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    'TMPL_L_DELIM'          =>  '{',        // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}',          // 模板引擎普通标签结束标记
	
	'LOAD_EXT_CONFIG'		=> 'db_dev,content_dev,redis_dev,privilege_dev',
	//返回压缩设置
	'GZIP_CONFIG' =>'OFF',
	'GZIP_PREFIX' =>'iknowdoctor.com',	//gzip压缩时的前缀，15位
	'H5CHECKLIST' => array(
		'/info/NewsDetail/showDetail',
	));
}

return $config_all;
