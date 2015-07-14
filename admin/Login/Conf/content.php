<?php

$cache_redis = array(
	'type'=>'Redis',
	'host'=>'127.0.0.1',
	'port'=>'6379',
	'prefix'=>'redis',
	'expire'=>0
);

return array(
	'CACHE_REDIS' => C('TOKEN_REDIS')
);