<?php

$secret_theme = array(
	array('id'=>101, 'img'=>'101.jpg'),
	array('id'=>102, 'img'=>'102.jpg'),
	array('id'=>103, 'img'=>'103.jpg'),
	array('id'=>104, 'img'=>'104.jpg'),
	array('id'=>105, 'img'=>'105.jpg'),
	array('id'=>106, 'img'=>'106.jpg'),
	array('id'=>107, 'img'=>'107.jpg'),
	array('id'=>108, 'img'=>'108.jpg'),
	array('id'=>109, 'img'=>'109.jpg'),
	array('id'=>110, 'img'=>'110.jpg'),
	array('id'=>111, 'img'=>'111.jpg'),
	array('id'=>112, 'img'=>'112.jpg')
); 

return array(
	//'配置项'=>'配置值'
	'IMG_PATH' => ROOT_PATH.'/admin_imed_me/res/focuslist/',
	'SECRET_THEME'	=> $secret_theme,
	'SECRET_USR_ID'	=> 58		//默认发帖用户ID
);