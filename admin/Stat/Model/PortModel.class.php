<?php
namespace Stat\Model;
use Think\Model;

class PortModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_ajaxreturn_error';

    public function calDaily()
    {
        $sql = "SELECT MAX(datestamp) datestamp FROM t_ajaxreturn_error_daily";
        $re_date = $this->query($sql);
        if($re_date === false)
            return array('code'=>-1, 'message'=>'查询错误：' . $sql);
        if(count($re_date) == 0)
        {
            $sql = "SELECT DISTINCT SUBSTRING(CAST(log_time AS CHAR(20)), 1, 10) AS error_date FROM t_ajaxreturn_error";
        }
        else
        {
            $max_date = $re_date[0]['datestamp'];
            $min_time = date("Y-m-d H:i:s", strtotime($max_date) + 86400);
            var_dump($max_date);
            var_dump($min_time);
            $sql = <<<EOF
            SELECT DISTINCT SUBSTRING(CAST(log_time AS CHAR(20)), 1, 10) AS error_date 
            FROM t_ajaxreturn_error
            WHERE log_time > '{$min_time}'
EOF;
        }
        $date_list = $this->query($sql);
        $failure_date = "";
        for($i = 0; $i < count($date_list); $i++)
        {
            $datestamp = $date_list[$i]['error_date'];
            $bgn_time = $datestamp . " 00:00:00";
            $end_time = date('Y:m:d H:i:s', strtotime($bgn_time) + 86400);
            $cal_sql = <<<EOF
            INSERT INTO t_ajaxreturn_error_daily(datestamp, error_num, port_num)
            SELECT '{$datestamp}' datestamp, COUNT(*) error_num, COUNT(DISTINCT req_url) port_num 
            FROM t_ajaxreturn_error 
            WHERE log_time > '{$bgn_time}' and log_time < '{$end_time}'
EOF;
            $re = $this->execute($cal_sql);
            if(empty($re))
                $failure_date .= $datestamp . ", ";
        }
        if($failure_date != '')
            return array('code'=>-2, 'message'=>"插入表数据错误：" . $failure_date);
        else
            return array('code'=>1, 'message'=>"执行成功");
    }

}
