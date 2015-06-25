<?php
namespace Import\Model;
use Think\Model;

class ImportModel extends Model{
	protected $connection = 'DB_ADMIN';
    protected $trueTableName = 't_info_raw';

    public function intoTable($sql)
    {
    	$tmpSql = "SET NAMES 'utf8';";
        $this->execute($tmpSql);
    	$re = $this->execute($sql);
    	return $re;
    }

}