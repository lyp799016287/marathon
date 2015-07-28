<?php
namespace Visual\Model;
use Think\Model;

class DescModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_device_flow';

    public function sdkData($type)
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
        $sql = <<<EOF
        SELECT a.{$field}, count(a.user_uid) part_num FROM 
        (SELECT DISTINCT user_uid, user_mobile, sdk, sys_version, app_version FROM t_user_device_flow) a 
        GROUP BY a.{$field}
EOF;
        $result = $this->query($sql);
        return $result;
    }

    ## 手机型号分布
    public function modelData()
    {
        
    }

    public function retainData()
    {
        $sql = <<<EOF
        SELECT a.register_date, b.new_user, a.retain_2, a.retain_3, a.retain_7, a.retain_30 
        FROM (SELECT register_date, CASE WHEN retain_2 IS NULL THEN 0 ELSE retain_2 END retain_2, CASE WHEN retain_3 IS NULL THEN 0 ELSE retain_3 END retain_3, 
        CASE WHEN retain_7 IS NULL THEN 0 ELSE retain_7 END retain_7, CASE WHEN retain_30 IS NULL THEN 0 ELSE retain_30 END retain_30
        FROM t_user_retain ORDER BY register_date DESC LIMIT 7) a 
        LEFT JOIN (SELECT datestamp, new_user FROM t_user_summary) b 
        ON a.register_date = b.datestamp 
EOF;
        $re = $this->query($sql);
        return $re;
    }

    public function timeData()
    {
        $sql = "SELECT hour_tag, SUM(freq_num) freq_num FROM t_user_time GROUP BY hour_tag";
        $re = $this->query($sql);
        return $re;
    }
}
