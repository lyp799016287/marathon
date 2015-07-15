<?php
namespace Stat\Model;
use Think\Model;

class ShareModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_share';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function shareDaily()
    {
        ## 确定需要计算的开始时间
        $sql = "SELECT MAX(datestamp) `date` FROM t_share_daily";
        $re = queryByNoModel('t_share_daily', '', $this->stat_config, $sql);
        if($re === false)
            return false;
        $max_date = '';
        if(!is_null($re[0]['date']))
        {
            $max_date = $re[0]['date'];
            $max_date = date("Y-m-d", strtotime($max_date) + 86400);
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) `date` FROM t_share";
            $re = $this->query($sql);
            if($re === false)
                return false;
            if(!is_null($re[0]['date']))
                $max_date = $re[0]['date'];
            else
                return;
        }
        // var_dump($max_date); exit;
        ## 计算每天的数据 插入表stat.t_share_daily
        $now_date = date("Y-m-d", time());
        while($max_date < $now_date)
        {
            $bgn_date = $max_date . " 00:00:00";
            $end_date = date("Y-m-d H:i:s", strtotime($bgn_date));
            $type_sql = "SELECT DISTINCT `type` FROM t_share WHERE create_time >= '" . $bgn_date . "' AND create_time <'" . $end_date . "'";
            $channel_sql = "SELECT DISTINCT `channel` FROM t_share WHERE create_time >= '" . $bgn_date . "' AND create_time <'" . $end_date . "'";
            $type_result = $this->query($type_sql);
            $channel_result = $this->query($channel_sql);
            if($type_result === false || $channel_result === false)
                return false;
            var_dump(type_result); 
//             for($i = 0; $i < count($type_result); $i++)
//                 for($j = 0; $j < count($channel_result); $j++)
//                 {
//                     $type = $type_result[$i]['type'];
//                     $channel = $channel_result[$j]['channel'];
//                     $sql = <<<EOF
//                     SELECT COUNT(DISTINCT user_id) uv, COUNT(user_id) pv 
//                     FROM imed.`t_share` WHERE create_time >= '{$bgn_date}' AND create_time < '{$end_date}';
// EOF;
//                     $tmp_re = $this->query($sql);
//                     if($tmp_re === false)
//                         return false;
//                     $insert_data = array();
//                     $insert_data['datestamp'] = $bgn_date;
//                     $insert_data['type'] = $type;
//                     $insert_data['channel'] = $channel;
//                     $insert_data['pv'] = $tmp_re[0]['pv'];
//                     $insert_data['uv'] = $tmp_re[0]['uv'];
//                     $insert_re = insertByNoModel('t_share_daily', '', $this->stat_config, $insert_data); 
//                     if($insert_re === false)
//                         return false;
//                 }

            $max_date = date('Y-m-d', strtotime($max_date) + 86400);
        }
        return true;
    }



    private function updateTable($table, $condition, $data)
    {
        $obj_mod = M($table, '', $this->stat_config);
        $result = $obj_mod->where($condition)->save($data);
        return $result;
    }

}
