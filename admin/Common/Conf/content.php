<?php

/*$ZX_CATE_LIST = array(
	'0'=> '',
	'1'=> '推广',
	'2'=> '深度解读',
	'3'=> '指南',
	'4'=> '临床研究',
	'5'=> '病例分析',
	'6'=> '会议',
	'7'=> '推荐',
	'8'=> '热点'
);*/

$ZX_CATE_LIST = array(
	'0'=> '无',
	'1'=>'新闻',
	'2'=> '心血管资讯'
);

$ZX_LABEL_LIST = array(
	'0'=> '无',
	'1'=> '推广',
	'2'=> '职业化培训',
	'3'=> '指南共识',
	'4'=> '临床研究',
	'5'=> '病例',
	'6'=> '会议',
	'7'=> '专家课堂',
	'8'=> '热点',
	'9'=> '综述'
);

//类别、标签对应关系
$ZX_CATE_LABEL_LIST = array(
	'0' => '0,2',
	'1' => '0,1,8',
	'2' => '0,3,4,5,6,7,9'
);

//秘密评论设定的运营账号(1100-1109)
$SECRET_COMMENT_UID = array(1100, 1101, 1102, 1103, 1104, 1105, 1106, 1107, 1108, 1109);

return array(
	'IMG_DOMAIN' => 'http://d.imed.me',
	'ZX_DOMAIN'  => 'http://d.imed.me',
	'ZX_LABEL_LIST'		=> $ZX_LABEL_LIST,
	'ZX_CATE_LABEL_LIST'=> $ZX_CATE_LABEL_LIST,
	'ZX_CATE_LIST'	=> $ZX_CATE_LIST,
	'SECRET_COMMENT_UID' => $SECRET_COMMENT_UID
);
