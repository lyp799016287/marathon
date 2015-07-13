<?php
namespace Stat\Model;
use Think\Model;

class UserModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_user_info';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function calSummary()
    {
        $sql = "SELECT MAX(datestamp) datestamp FROM t_user_summary";
        $date_info = queryByNoModel('t_user_summary', '', $this->stat_config, $sql);
        if($date_info === false)
            return false;
        $str_tmp = "";
        $now_date = date("Y-m-d", time());
        if(count($date_info) > 0)
        {
            $str_tmp = date("Y-m-d", strtotime($date_info[0]['datestamp']) + 86400);
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) stamp FROM t_user_info WHERE `status` = 1";
            $re = $this->query($sql);
            if($re === false)
                return false;
            $str_tmp = $re[0]['stamp'];
        }

        $insert_data = array();
        $i = 0;
        while($str_tmp < $now_date)
        {
            $endstamp = date("Y-m-d", strtotime($str_tmp) + 86400);
            $endstamp = $endstamp . " 00:00:00";
            ## 计算当天的累计用户数
            $cumul_re = $this->cumulative_user($endstamp);
            if($cumul_re === false)
                return false;
            $insert_data[$i]['cumulation_user'] = $cumul_re[0]['cumulation_user'];
            $insert_data[$i]['datestamp'] = $str_tmp;
            ## 计算当天的新增用户数
            $new_re = $this->new_user($str_tmp, $endstamp);
            if($new_re === false)
                return $new_re;
            $insert_data[$i]['new_user'] = $new_re[0]['new_user'];
            ## 计算当天的活跃用户数（当天有打开APP的算作活跃用户）
            $active_re = $this->active_user($str_tmp, $endstamp);
            if($active_re === false)
                return false;
            $insert_data[$i]['active_user'] = $active_re[0]['active_user'];

            $i++;
            $str_tmp = $endstamp;
        }
        return $this->insert_summary($insert_data);
    }


    private function cumulative_user($endstamp)
    {
        $sql = "SELECT COUNT(*) cumulation_user FROM t_user_info WHERE create_time < '" . $endstamp;
        $cumul_re = $this->query($sql);
        return $cumul_re;
    }

    private function new_user($bgn_date, $end_stamp)
    {
        $bgn_stamp = $bgn_date . " 00:00:00";
        $sql = "SELECT COUNT(id) new_user FROM t_user_info WHERE `status` = 1 AND create_time >= '" . $bgn_stamp ."' AND create_time < '" . $end_stamp . "'";
        $re = $this->query($sql);
        return $re;
    }

    private function active_user($bgn_date, $end_stamp)
    {
        $bgn_stamp = $bgn_date . " 00:00:00";
        $sql = <<<EOF
        SELECT COUNT(DISTINCT user_uid) active_user 
        FROM t_login_flow 
        WHERE create_time >= '{$bgn_stamp}' AND create_time < '{$end_stamp}' AND `status` IN (1, 2)
EOF;
        $re = $this->query($sql);
        return $re;
    }

    private function insert_summary($data)
    {
        for($i = 0; $i < count($data); $i++)
        {
            $insert_data = $data[$i];
            $re = insertByNoModel('t_user_summary', '', $this->stat_config, $insert_data); 
            if($re === false)
                return false;
        }
        return true;
    }

   

}
