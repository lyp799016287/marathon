<?php
namespace Visual\Model;
use Think\Model;

class DescModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_device_flow';

    public function _initialize()
    {
        $this->imed_config = C('DB_IMED');
    }

    public function deviceData($type)
    {
        $field = "";
        switch($type)
        {
            case 1:
                $field = "sdk";
                break;
            case 2:
                $field = "sys_version";
                break;
            case 3:
                $field = "app_version";
                break;
            default:
                break;
        }
//         $sql = <<<EOF
//         SELECT a.{$field} field_name, count(a.user_uid) part_num FROM 
//         (SELECT DISTINCT user_uid, user_mobile, sdk, sys_version, app_version FROM t_user_device_flow) a 
//         GROUP BY a.{$field}
//         ORDER BY count(a.user_uid) DESC
// EOF;
        $sql = <<<EOF
        SELECT b.{$field} field_name, COUNT(a.user_uid) part_num FROM 
        (SELECT user_uid, MAX(login_time) latest_login_time FROM t_user_device_flow GROUP BY user_uid) a 
        LEFT JOIN t_user_device_flow b ON a.user_uid = b.user_uid AND a.latest_login_time = b.login_time
        GROUP BY b.{$field}
        ORDER BY COUNT(a.user_uid) DESC
EOF;
        $result = $this->query($sql);
        return $result;
    }

    ## 手机型号分布
    public function modelData()
    {
        
    }


    public function retainData($type)
    {
        if($type == 1)
            $limit = 8; ## 需要多查一天 第一条数据需要去掉
        elseif($type == 2)
            $limit = 31;
        else
            return false;
        $sql = <<<EOF
        SELECT a.register_date, b.new_user, a.retain_2, a.retain_3, a.retain_7, a.retain_30 
        FROM (SELECT register_date, CASE WHEN retain_2 IS NULL THEN 0 ELSE retain_2 END retain_2, CASE WHEN retain_3 IS NULL THEN 0 ELSE retain_3 END retain_3, 
        CASE WHEN retain_7 IS NULL THEN 0 ELSE retain_7 END retain_7, CASE WHEN retain_30 IS NULL THEN 0 ELSE retain_30 END retain_30
        FROM t_user_retain ORDER BY register_date DESC LIMIT {$limit}) a 
        LEFT JOIN (SELECT datestamp, new_user FROM t_user_summary) b 
        ON a.register_date = b.datestamp 
EOF;
        // var_dump($sql);
        $re = $this->query($sql);
        return $re;
    }

    public function timeData()
    {
        $sql = "SELECT hour_tag, SUM(freq_num) freq_num FROM t_user_time GROUP BY hour_tag";
        $re = $this->query($sql);
        return $re;
    }

    public function shareData()
    {
        $sql = "SELECT `type`, `channel`, COUNT(DISTINCT user_id) uv, COUNT(id) pv FROM t_share GROUP BY `type`, `channel`";
        $re = queryByNoModel('t_share', '', $this->imed_config, $sql);
        return $re;
    }

}
