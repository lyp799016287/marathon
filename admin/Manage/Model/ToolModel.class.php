<?php
namespace Manage\Model;
use Think\Model;

class ToolModel extends Model {
	protected $connection = 'DB_ADMIN';
	protected $trueTableName = 't_page';

	public function _initialize()
    {
        $this->admin_config = C('DB_ADMIN');
    }

	

	/**
	*	合并新扫描到的url，并入page表
	*/
	public function mergeInfo($urls){
        $sql = "SELECT * from t_page order by id desc";
		$counter = 0;
		$info_has  =$this->getRows($sql);
		$has_urls = array();
		$diff_urls = array();
        //$info_has = $this->getRows($sql);
        if($info_has === false)
            return array('code'=>-15, 'message'=>"查询错误：" . $sql);

        for($i = 0; $i < count($info_has); $i++){
            array_push($has_urls, $info_has[$i]['url']);
        }

        if(empty($has_urls)){
        	$diff_urls = $urls;
        }else{
        	$diff_urls = array_diff($urls,$has_urls);
        }

        $sql = "insert into t_page (name,url) values ";
        $sqlarray = array();
        foreach ($diff_urls as $key => $value) {
        	array_push($sqlarray, "('{$value}','{$value}')");
        }
        $sql .= implode(',', $sqlarray);
        $counter=0;
        if(!empty($sqlarray)){
        	 $counter = $this->exeSql($sql);
        }

        if($counter ===false){
        	 return array('code'=>-1, 'message'=>"更新错误");
        }

        return array('code'=>1, 'message'=>"共同步 {$counter} 条数据");
    }

	private function getRows($sql){

		$this->execute("SET NAMES utf8");

		$rs = $this->query($sql);
		return $rs;
	}

	private function exeSql($sql,$parse=false){
		
		$this->execute("SET NAMES utf8");

		$rs = $this->execute($sql,$parse=false);
		return $rs;
	}
}