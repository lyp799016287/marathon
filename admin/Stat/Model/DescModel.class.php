<?php
namespace Stat\Model;
use Think\Model;

class DescModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_login_flow';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function calDevice()
    {
        $sql = "SELECT MAX(login_time) login_time FROM t_user_device_flow";
        $re = queryByNoModel('t_user_device_flow', '', $this->stat_config, $sql);
        if($re === false)
            return array('code'=>-1, 'message'=>"查询错误：" . $sql);
        $where_clause = ' 1';
        if(!is_null($re[0]['login_time']))
        {
            $max_time = $re[0]['login_time'];
            $where_clause = " create_time >'" . $max_time . "'";
        }
        $select_sql = <<<EOF
        SELECT user_uid, device_token, create_time FROM imed.`t_login_flow` 
        WHERE `status` IN (1, 2) AND {$where_clause}
EOF;
        $flow = $this->query($select_sql);
        if($flow === false)
            return array('code'=>-2, 'message'=>"查询错误：" . $select_sql);
        for($i = 0; $i < count($flow); $i++)
        {
            $insert_data = array();
            $insert_data['user_uid'] = $flow[$i]['user_uid'];
            $insert_data['login_time'] = $flow[$i]['create_time'];
            $device_ary = explode('_', $flow[$i]['device_token']);
            if(count($device_ary) != 4)
                continue;
            for($k = 0; $k < count($device_ary); $k++)
            {
                switch($k)
                {
                    case 0:
                        $insert_data['user_mobile'] = $device_ary[$k];
                        break;
                    case 1:
                        $insert_data['sdk'] = $device_ary[$k];
                        break;
                    case 2:
                        $insert_data['sys_version'] = $device_ary[$k];
                        break;
                    case 3:
                        $insert_data['app_version'] = $device_ary[$k];
                        break;
                    default:
                        break;
                }
            }
            // var_dump($insert_data); exit;
            $result = insertByNoModel('t_user_device_flow', '', $this->stat_config, $insert_data);
            if($result === false)
                return array('code'=>-3, 'message'=>"插入数据表错误：" . 't_user_device_flow');
        }
        return array('code'=>1, 'message'=>'执行成功');
    }

}
