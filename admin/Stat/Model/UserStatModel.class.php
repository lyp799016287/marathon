<?php
namespace Stat\Model;
use Think\Model;

class UserStatModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_summary';

    public function _initialize()
    {
        $this->imed_config = C('DB_IMED');
        // var_dump($stat_config);
    }

    ## 
    public function calLatestLogin()
    {
        $sql = "SELECT MIN(cal_time) cal_time FROM t_user_latest_login";
        $min_re = $this->query($sql);
        if($min_re === false)
            return array('code'=>-1, 'message'=>'查询失败');
        $min_time = '';
        if(is_null($min_re[0]['cal_time']))
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) min_date FROM t_login_flow WHERE `status` IN(1,2)";
            $re = queryByNoModel('t_login_flow', '', $this->imed_config, $sql);
            if($re === false)
                return array('code'=>-2, 'message'=>'查询失败');
            $min_time = date('Y-m-d', strtotime("+30 days", strtotime($re[0]['min_date']))); ## 计算30天之内的用户流失
        }
        else
            $min_time = date('Y-m-d', $min_re[0]['cal_time']);
        // var_dump($min_time); exit;
        $now_time = date('Y-m-d', time());
        while($min_time < $now_time)
        {
            $end_time = date('Y-m-d', strtotime("+1 day", strtotime($min_time)));
            $insert_sql = <<<EOF
            INSERT INTO t_user_latest_login(user_uid, latest_time, cal_time)
            SELECT user_uid, MAX(create_time) latest_time, '{$min_time}' 
            FROM imed.t_login_flow WHERE `status` IN(1, 2) AND create_time < '{$end_time}'
            GROUP BY user_uid
EOF;
            // var_dump($insert_sql); exit;
            $insert_re = $this->execute($insert_sql);
            if($insert_re === false)
                return array('code'=>-3, 'message'=>'数据插入失败');
            $min_time = $end_time;
        }

        $update_sql = <<<EOF
        UPDATE t_user_latest_login
        SET day_interval = DATEDIFF(cal_time, latest_time)
        WHERE day_interval IS NULL 
EOF;
        $update_re = $this->execute($update_sql);
        if($update_re === false)
            return array('code'=>-4, 'message'=>'数据更新失败');
        return array('code'=>1, 'message'=>'执行成功');
    }

}
