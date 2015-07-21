<?php

$secret_theme = array(
	array('id'=>0, 'img'=>'#7e88e9'),
	array('id'=>1, 'img'=>'#00a3ff'),
	array('id'=>2, 'img'=>'#30c4ea'),
	array('id'=>3, 'img'=>'#52de7e'),
	array('id'=>4, 'img'=>'#ff6379'),
	array('id'=>5, 'img'=>'#ff6d42'),
	array('id'=>6, 'img'=>'#feb100'),
	array('id'=>7, 'img'=>'#4e4e4e')
); 

return array(
	//'配置项'=>'配置值'
	'IMG_PATH' => ROOT_PATH.'/admin_imed_me/res/focuslist/',
	'SECRET_THEME'	=> $secret_theme,
	'SECRET_USR_ID'	=> 58		//默认发帖用户ID
);