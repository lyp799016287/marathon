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
        var_dump($date_info);
        if($date_info === false)
            return false;
        $str_tmp = "";
        $now_date = date("Y-m-d", time());
        if(count($date_info) > 0)
        {
            $str_tmp = date("Y-m-d", strtotime($date_info[0]['datestamp']) + 86400);
            var_dump($str_tmp);
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) stamp FROM t_user_info WHERE `status` = 1";
            $re = $this->query($sql);
            var_dump($re);
            if($re === false)
                return false;
            $str_tmp = $re[0]['stamp'];
        }
        var_dump($str_tmp);exit;

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
            $insert_data[$i]['datestamp'] = date("Y-m-d", strtotime($str_tmp));
            ## 计算当天的新增用户数
            $new_re = $this->new_user($str_tmp, $endstamp);
            if($new_re === false)
                return $new_re;
            if(count($new_re) == 0)
                $insert_data[$i]['new_user'] = 0;
            else
                $insert_data[$i]['new_user'] = $new_re[0]['new_user'];
            ## 计算当天的登录用户数、活跃用户数（=当天有打开APP的登录用户数-新增用户数）
            $login_re = $this->login_user($str_tmp, $endstamp);
            if($login_re === false)
                return false;
            if(count($login_re) == 0)
            {
                $insert_data[$i]['login_user'] = 0;
                $insert_data[$i]['active_user'] = 0;
            }  
            else
            {
                $insert_data[$i]['login_user'] = $login_re[0]['login_user'];
                $insert_data[$i]['active_user'] = $login_re[0]['login_user'] - $insert_data[$i]['new_user'];
            }
                

            $i++;
            $str_tmp = $endstamp;

        }
        var_dump($insert_data);
        return $this->insert_summary($insert_data);
    }


    private function cumulative_user($endstamp)
    {
        $sql = "SELECT COUNT(*) cumulation_user FROM t_user_info WHERE `status` = 1 AND create_time < '" . $endstamp . "'";
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

    private function login_user($bgn_date, $end_stamp)
    {
        $bgn_stamp = $bgn_date . " 00:00:00";
        $sql = <<<EOF
        SELECT COUNT(DISTINCT user_uid) login_user 
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

    public function calRetain()
    {
        
        $sql = <<<EOF
        SELECT MIN(register_date) `date` FROM t_user_retain 
        WHERE retain_2 IS NULL OR retain_3 IS NULL OR retain_7 IS NULL OR retain_30 IS NULL
EOF;
        $re = queryByNoModel('t_user_retain', '', $this->stat_config, $sql);
        if($re === false)
            return false;
        ## update表中的记录
        if(count($re) > 0)
        {
            $sql = "SELECT * FROM t_user_retain WHERE register_date >= '" . $re[0]['date'] . "'";
            $date_info = queryByNoModel('t_user_retain', '', $this->stat_config, $sql);
            if($date_info === false)
                return false;
            for($i = 0; $i < count($date_info); $i++)
            {
                $reg_date = $date_info[$i]['register_date'];
                $end_date = date("Y-m-d", strtotime("-1 day"));
                $day_interval = (strtotime($end_date) - strtotime($reg_date)) / 86400; # 时间间隔
                $update_date['retain_2'] = 0;
                $update_date['update_2'] = 0;
                $update_date['retain_3'] = 0;
                $update_date['update_3'] = 0;
                $update_date['retain_7'] = 0;
                $update_date['update_7'] = 0;
                $update_date['retain_30'] = 0;
                $update_date['update_30'] = 0;

                if(is_null($date_info[$i]['retain_2']) && $day_interval >= 2)
                {
                    $re = $this->calRetainNum($reg_date, 2);
                    if($re === false)
                        return false;
                    $update_date['retain_2'] = $re[0]['total_retain'];
                    $update_date['update_2'] = 1;
                }
                if(is_null($date_info[$i]['retain_3']) && $day_interval >= 3)
                {
                    $this->calRetainNum($reg_date, 3);
                    if($re === false)
                        return false;
                    $update_date['retain_3'] = $re[0]['total_retain'];
                    $update_date['update_3'] = 1;
                }
                if(is_null($date_info[$i]['retain_7']) && $day_interval >= 7)
                {
                    $this->calRetainNum($reg_date, 7);
                    if($re === false)
                        return false;
                    $update_date['retain_7'] = $re[0]['total_retain'];
                    $update_date['update_7'] = 1;
                }
                if(is_null($date_info[$i]['retain_30']) && $day_interval >= 30)
                {
                    $this->calRetainNum($reg_date, 30);
                    if($re === false)
                        return false;
                    $update_date['retain_30'] = $re[0]['total_retain'];
                    $update_date['update_30'] = 1;
                }
                ## update table
                if($update_date['update_2'] == 1)
                    $data['retain_2'] = $update_date['retain_2'];
                if($update_date['update_3'] == 1)
                    $data['retain_3'] = $update_date['retain_3'];
                if($update_date['update_7'] == 1)
                    $data['retain_7'] = $update_date['retain_7'];
                if($update_date['update_30'] == 1)
                    $data['retain_30'] = $update_date['retain_30'];

                $data['modify_time'] = now();
                $condition['register_date'] = $reg_date;
                $table = 't_user_retain';
                $update_re = $this->updateTable($table, $condition, $data);
                if($update_re === false)
                    return false;
            }
        }
        ## insert表中的记录
        $sql = "SELECT MAX(register_date) `date` FROM t_user_retain";
        $re = queryByNoModel('t_user_retain', '', $this->stat_config, $sql);
        if($re === false)
            return false;
        if(count($re) > 0)
        {
            $max_date = $re[0]['date'];
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) `date` FROM `t_user_info`";
            $min_date = $this->query($sql);
            if($min_date === false)
                return false;
            if(count($min_date) > 0)
            {
                $min_date = $min_date[0]['date'];
                $max_date = $min_date;
            }
            else
                return;
        }

        $next_date = date('Y-m-d', strtotime($max_date) + 86400);
        $now_date = date('Y-m-d', now());
        while($now_date > $next_date)
        {              
            $day_interval = (strtotime($now_date) - strtotime($max_date)) / 86400;
            $data = $this->getInsertData($max_date, $day_interval);
            $re = insertByNoModel('t_user_retain', '', $this->stat_config, $data); 
            if($re === false)
                return false;

            $max_date = date('Y-m-d', strtotime($max_date) + 86400);
            $next_date = date('Y-m-d', strtotime($next_date) + 86400);
        }
        
        return true;
    }


    private function getInsertData($date, $day_interval)
    {
        $insert_data = array();
        $insert_data['register_date'] = $date;
        if($day_interval >= 30)
        {
            $retain_30 = $this->calRetainNum($date, 30);
            if($retain_30 === false)
                return false;
            $insert_data['retain_30'] = $retain_30;

            $retain_7 = $this->calRetainNum($date, 7);
            if($retain_7 === false)
                return false;
            $insert_data['retain_7'] = $retain_7;

            $retain_3 = $this->calRetainNum($date, 3);
            if($retain_3 === false)
                return false;
            $insert_data['retain_3'] = $retain_3;

            $retain_2 = $this->calRetainNum($date, 2);
            if($retain_2 === false)
                return false;
            $insert_data['retain_2'] = $retain_2;
        }
        elseif($day_interval >= 7)
        {
            $retain_7 = $this->calRetainNum($date, 7);
            if($retain_7 === false)
                return false;
            $insert_data['retain_7'] = $retain_7;

            $retain_3 = $this->calRetainNum($date, 3);
            if($retain_3 === false)
                return false;
            $insert_data['retain_3'] = $retain_3;

            $retain_2 = $this->calRetainNum($date, 2);
            if($retain_2 === false)
                return false;
            $insert_data['retain_2'] = $retain_2;
        }
        elseif($day_interval >= 3)
        {
            $retain_3 = $this->calRetainNum($date, 3);
            if($retain_3 === false)
                return false;
            $insert_data['retain_3'] = $retain_3;

            $retain_2 = $this->calRetainNum($date, 2);
            if($retain_2 === false)
                return false;
            $insert_data['retain_2'] = $retain_2;      
        }
        elseif($day_interval >= 2)
        {
            $retain_2 = $this->calRetainNum($date, 2);
            if($retain_2 === false)
                return false;
            $insert_data['retain_2'] = $retain_2;
        }
        return $insert_data;
    }

    private function calRetainNum($reg_date, $day_interval)
    {
        $time1 = $reg_date . " 00:00:00";
        $time2 = date("Y-m-d H:i:s", strtotime($time1) + 86400);
        $time3 = date("Y-m-d H:i:s", strtotime($time1) + 86400 * $day_interval);
        $sql = <<<EOF
        SELECT COUNT(a.user_uid) total_retain FROM
        (SELECT user_uid FROM imed.t_user_info WHERE create_time >= {$time1} AND create_time < {$time2} AND `status` = 1) a
        LEFT JOIN (
        SELECT user_uid, MIN(create_time) create_time FROM imed.`t_login_flow` 
        WHERE `status` IN (1, 2) AND create_time >= {$time2} AND create_time < {$time3}
        GROUP BY user_uid ) b ON a.user_uid = b.user_uid WHERE b.create_time IS NOT NULL;
EOF;
        return $this->query($sql);
    }

    private function updateTable($table, $condition, $data)
    {
        $obj_mod = M($table, '', $this->stat_config);
        $result = $obj_mod->where($condition)->save($data);
        return $result;
    }

}
