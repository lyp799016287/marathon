<?php
namespace Visual\Model;
use Think\Model;

class PortModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_ajaxreturn_error';

    // public function _initialize()
    // {
    //     $this->imed_config = C('DB_IMED');
    //     $this->topNum = 10; ## top显示的条数
    // }

    private function queryFunction($sql)
    {
        $tmp_sql = "SET NAMES utf8";
        $this->execute($tmp_sql);
        return $this->query($sql);
    }

    public function calPeriod($idx, $type)
    {
        // $before_date = date("Y-m-d", strtotime("-1 day")); ## 前一天对应的日期
        if($type == 1)
            $bgn_date = date('Y-m-d', strtotime("-7 days"));
        elseif($type == 2)
            $bgn_date = date('Y-m-d', strtotime("-7 weeks"));
        elseif($type == 3)
            $bgn_date = date('Y-m-d', strtotime("-7 months"));
        else
            return false;

        $sql = "SELECT datestamp, error_num, port_num FROM t_ajaxreturn_error_daily WHERE log_time >= '" . $bgn_date . "'";
        $result = $this->queryFunction($sql);
        return $result;
    }

    public function calDetail()
    {
        $start = ($data['current_page'] - 1) * $data['page_size'];
        $limit = ' LIMIT '.$start.', '.$data['page_size'];

        $order = '';
        if(isset($data['sort_name']) && !empty($data['sort_name']))
            $order .= ' ORDER BY '.$data['sort_name'];
        else
            $order .= ' ORDER BY log_time';

        if(isset($data['sort_order']) && !empty($data['sort_order']))
            $order .= ' '.$data['sort_order'];
        else
            $order .= ' DESC';
        $sql = <<<EOF
            SELECT req_url, log_time, req_param, req_uid, resp_str, resp_sessionId, resp_sessionName, 
            mobile_type, sys_version, sdk_version, app_version 
            FROM t_ajaxreturn_error 
            WHERE log_time >= '{$bgn_date}'
            {$order}{$limit}
EOF;
        $re = $this->queryFunction($sql);
        return $re;
    }

}
