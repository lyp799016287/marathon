<?php

$cache_redis = array(
	'type'=>'Redis',
	'host'=>'127.0.0.1',
	'port'=>'6379',
	'prefix'=>'redis',
	'expire'=>0
);

$db_stat = array(
	'db_type'  => 'mysql',
    'db_user'  => 'yzweb',
    'db_pwd'   => 'yzweb~123',
    'db_host'  => '192.168.16.221',
    'db_port'  => '3306',
    'db_name'  => 'stat'
	);

return array(
	'CACHE_REDIS' => C('TOKEN_REDIS'),
	'DB_STAT' => $db_stat
);