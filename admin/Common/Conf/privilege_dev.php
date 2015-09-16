<?php
return array(
	//'配置项'=>'配置值'
	//权限分配设置
    'USER_AUTH_ON'			=>true, //是否需要认证
    'USER_AUTH_TYPE'		=>1, //认证类型
	'ADMIN_AUTH_KEY'		=>'admin',
    'USER_AUTH_KEY'			=>'user_id',  // 认证识别号
    'USER_AUTH_MODEL'		=>'t_user',//模型实例（用户表名）
    'REQUIRE_AUTH_MODULE'	=>'',  //需要认证模块
    'NOT_AUTH_MODULE'		=>'',   //无需认证模块
    'NOT_AUTH_ACTION'		=> '',
	'REQUIRE_AUTH_ACTION'	=>'',
	'USER_AUTH_GATEWAY'		=>'', //认证网关
    //RBAC_DB_DSN  数据库连接DSN	
    'RBAC_ROLE_TABLE'		=>'think_role', //角色表名称
    'RBAC_USER_TABLE'		=>'think_role_user', //用户和角色对应关系表名称
    'RBAC_ACCESS_TABLE'		=>'think_access', //权限分配表名称
    'RBAC_NODE_TABLE'		=>'think_node',  // 权限表名称
);