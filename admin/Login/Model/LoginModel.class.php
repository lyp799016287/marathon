<?php
namespace Login\Model;
use Think\Model;

class LoginModel extends Model {
    protected $connection = 'DB_ADMIN';
    protected $trueTableName = 't_user';

    public function loginVarify($name, $psw)
    {
        $selectSql = "SELECT * FROM t_user WHERE user_name = '" . $name . "' AND user_psw = '" . $psw . "' AND `status` = 2";
        $re = $this->query($selectSql);
        if($re)
            //return true;
			return $re;
        else
        {
            $seSql = "SELECT * FROM t_user WHERE user_name = '" . $name . "' AND `status` = 1";
            $result = $this->query($seSql);
            if($result) 
                return -1; ## 密码错误
            else
                return -2; ## 未注册
        }
    }
        
    public function addFlow($name, $time, $status)
    {
        $insertSql = "INSERT INTO t_login_flow(user_name, login_time, login_status) VALUES ('" . $name . "', '" . $time . "', " . $status . ")";
        $re = $this->execute($insertSql);
        return $re;
    }

    public function isRegVarify($name)
    {
        
        $seSql = "SELECT * FROM t_user WHERE user_name = '" . $name . "'";
        $result = $this->query($seSql);
        if($result) 
            return true;
        else
            return false;
       
    }


}
