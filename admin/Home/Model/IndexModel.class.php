<?php
namespace Home\Model;
use Think\Model;

class IndexModel extends Model {
    protected $connection = 'DB_ADMIN';
    protected $trueTableName = 't_menu';

    public function _initialize()
    {
        $this->admin_config = C('DB_ADMIN');
    }

    

    /**
    *   获取菜单信息
    */
    public function getMenu(){
        $sql = "SELECT * from t_menu where status=1 order by id,parent_id";
        $counter = 0;
        $menus  =$this->getRows($sql);
        
        //$menus = $this->getRows($sql);
        if($menus === false){
            return false;
        }
        else{
            return $menus;
        }

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