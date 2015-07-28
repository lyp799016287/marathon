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
        GROUP BY a.sdk
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
        
    }
}
