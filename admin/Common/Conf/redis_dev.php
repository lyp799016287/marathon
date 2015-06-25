<?php
/*
 *  @author Step @ 2015-4-28
 *  
 */

$TOKEN_REDIS = array(
		'type'=>'Redis',
		'host'=>'127.0.0.1',
		'port'=>'6379',
		'prefix'=>'mtoken_',
		'expire'=>60
);

return array('TOKEN_REDIS'=>$TOKEN_REDIS);