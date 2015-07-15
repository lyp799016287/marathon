<?php
namespace Stat\Model;
use Think\Model;

class InfoModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_info_comment';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function calScanDaily()
    { 
        $now_date = date('Y-m-d', time());
        $end_time = $now_date . " 00:00:00"; ## 在当天时间之前的日期
        $sql = <<<EOF
        SELECT DISTINCT SUBSTRING(CAST(scan_time AS CHAR(20)), 1, 10) AS scan_date 
        FROM t_scan_flow WHERE scan_time < '{$end_time}'
EOF;
        $info_flow = queryByNoModel('t_scan_flow', '', $this->stat_config, $sql);
        if($info_flow === false)
            return false;
        $sql = "SELECT DISTINCT scan_date FROM t_scan_flow_daily";
        $info_daily = queryByNoModel('t_scan_flow_daily', '', $this->stat_config, $sql);
        if($info_daily === false)
            return false;
        $date_info = array(); ## 存放没有统计daily数据的date日期
        for($i = 0; $i < count($info_flow); $i++)
        {
            $flag = 0;
            for($j = 0; $j < count($info_daily); $j++)
                if($info_daily[$j]['scan_date'] == $info_flow[$i]['scan_date'])
                {
                    $flag++;
                    break;
                }
            if($flag == 0)
                $date_info[] = $info_flow[$i]['scan_date'];
        }
        // var_dump($date_info);
        for($i = 0; $i < count($date_info); $i++)
        {
            ## 每天各个资讯的uv/pv统计
            $date = $date_info[$i];
            $bgn_time = $date . " 00:00:00";
            $end_time = $date . " 23:59:59";
            $sql = <<<EOF
            SELECT info_id, COUNT(DISTINCT uid) uv, COUNT(id) pv FROM t_scan_flow
            WHERE scan_time >= '{$bgn_time}' AND scan_time <= '{$end_time}'
            GROUP BY info_id
EOF;
            $re = queryByNoModel('t_scan_flow', '', $this->stat_config, $sql);
            if($re === false)
                return false;
            ## 用登录用户的pv量
            $sql = <<<EOF
            SELECT info_id, COUNT(id) no_login_pv FROM t_scan_flow 
            WHERE uid = 0 AND scan_time >= '{$bgn_time}' AND scan_time <= '$end_time'
            GROUP BY info_id
EOF;
            $re_no_login = queryByNoModel('t_scan_flow', '', $this->stat_config, $sql);
            if($re_no_login === false)
                return false;
            $re = $this->data_merge($re, $re_no_login);
            var_dump($re);
            ## 将当天的数据插入到表t_scan_flow_daily中
            for($k = 0; $k < count($re); $k++)
            {
                $insert_data = array();
                $insert_data['info_id'] = $re[$k]['info_id'];
                $insert_data['pv'] = $re[$k]['pv'];
                $insert_data['uv'] = $re[$k]['uv'];
                $insert_data['no_login_pv'] = $re[$k]['no_login_pv'];
                $insert_data['scan_date'] = $date;
                $insert_re = insertByNoModel('t_scan_flow_daily', '', $this->stat_config, $insert_data); 
                if($insert_re === false)
                    return false;
            }  
        }
        return $date_info;
    }


    private function data_merge($re1, $re2)
    {
        for($i = 0; $i < count($re1); $i++)
        {
            $info_id = $re1[$i]['info_id'];
            $flag = 0;
            for($j = 0; $j < count($re2); $j++)
                if($info_id == $re2[$j]['info_id'])
                {
                    $re1[$i]['no_login_pv'] = $re2[$j]['no_login_pv'];
                    $flag++;
                }
            if($flag == 0)
                $re1[$i]['no_login_pv'] = 0;
        }
        return $re1;
    }



    public function calCommentDaily()
    {
        $now_date = date('Y-m-d', time());
        $end_time = $now_date . " 00:00:00"; ## 在当天时间之前的日期
        $sql = <<<EOF
        SELECT DISTINCT SUBSTRING(CAST(`time` AS CHAR(20)), 1, 10) AS comment_date 
        FROM t_info_comment WHERE `time` < '{$end_time}'
EOF;
        $info_flow = $this->query($sql);
        if($info_flow == false)
            return false;
        $sql = "SELECT DISTINCT comment_date FROM t_info_comment_daily";
        $info_daily = queryByNoModel('t_info_comment_daily', '', $this->stat_config, $sql);
        if($info_daily === false)
            return false;
        $date_info = array(); ## 存放尚未统计daily数据的date日期
        for($i = 0; $i < count($info_flow); $i++)
        {
            $flag = 0;
            for($j = 0; $j < count($info_daily); $j++)
                if($info_daily[$j]['comment_date'] == $info_flow[$i]['comment_date'])
                {
                    $flag++;
                    break;
                }
            if($flag == 0)
                $date_info[] = $info_flow[$i]['comment_date'];
        }

        for($i = 0; $i < count($date_info); $i++)
        {
            ## 每天各个资讯的uv/pv统计
            $date = $date_info[$i];
            $bgn_time = $date . " 00:00:00";
            $end_time = $date . " 23:59:59";
            $sql = <<<EOF
            SELECT info_id, COUNT(DISTINCT user_id) uv, COUNT(comment_id) pv FROM t_info_comment
            WHERE `time` >= '{$bgn_time}' AND `time` <= '{$end_time}' AND `type` = 0 AND `status` = 1 
            GROUP BY info_id
EOF;
            $re = $this->query($sql);
            if($re === false)
                return false;
           
            ## 将当天的数据插入到表t_info_comment_daily中
            for($k = 0; $k < count($re); $k++)
            {
                $insert_data = array();
                $insert_data['info_id'] = $re[$k]['info_id'];
                $insert_data['pv'] = $re[$k]['pv'];
                $insert_data['uv'] = $re[$k]['uv'];
                $insert_data['comment_date'] = $date;
                $insert_re = insertByNoModel('t_info_comment_daily', '', $this->stat_config, $insert_data); 
                if($insert_re === false)
                    return false;
            }  
        }
        return $date_info;
    }

    public function calShareDaily()
    {
        $now_date = date('Y-m-d', time());
        $end_time = $now_date . " 00:00:00"; ## 在当天时间之前的日期
        $sql = <<<EOF
        SELECT DISTINCT SUBSTRING(CAST(`create_time` AS CHAR(20)), 1, 10) AS share_date 
        FROM t_share WHERE `create_time` < '{$end_time}'
EOF;
        $info_flow = $this->query($sql);
        if($info_flow == false)
            return false;

        $sql = "SELECT DISTINCT share_date FROM t_share_info_daily";
        $info_daily = queryByNoModel('t_share_info_daily', '', $this->stat_config, $sql);
        if($info_daily === false)
            return false;
        $date_info = array(); ## 存放尚未统计daily数据的date日期
        for($i = 0; $i < count($info_flow); $i++)
        {
            $flag = 0;
            for($j = 0; $j < count($info_daily); $j++)
                if($info_daily[$j]['share_date'] == $info_flow[$i]['share_date'])
                {
                    $flag++;
                    break;
                }
            if($flag == 0)
                $date_info[] = $info_flow[$i]['share_date'];
        }

        for($i = 0; $i < count($date_info); $i++)
        {
            ## 每天各个资讯的uv/pv统计
            $date = $date_info[$i];
            $bgn_time = $date . " 00:00:00";
            $end_time = $date . " 23:59:59";
            $sql = <<<EOF
            SELECT target_id info_id, COUNT(DISTINCT user_id) uv, COUNT(id) pv FROM t_share
            WHERE `create_time` >= '{$bgn_time}' AND `create_time` <= '{$end_time}' AND `type` = 2
            GROUP BY target_id
EOF;
            $re = $this->query($sql);
            if($re === false)
                return false;
           
            ## 将当天的数据插入到表t_share_info_daily中
            for($k = 0; $k < count($re); $k++)
            {
                $insert_data = array();
                $insert_data['info_id'] = $re[$k]['info_id'];
                $insert_data['pv'] = $re[$k]['pv'];
                $insert_data['uv'] = $re[$k]['uv'];
                $insert_data['share_date'] = $date;
                $insert_re = insertByNoModel('t_share_info_daily', '', $this->stat_config, $insert_data); 
                if($insert_re === false)
                    return false;
            }  
        }
        return $date_info;
    }


    public function mergeInfo()
    {
        ## 确定需要插入的数据时间域
        $sql = "SELECT MAX(datestamp) datestamp FROM t_info_daily";
        $date_info = queryByNoModel('t_info_daily', '', $this->stat_config, $sql);
        $time_str = "";
        if($date_info === false)
            return false;
        if(!is_null($date_info[0]['datestamp']))
            $time_str = " WHERE a.scan_date > '" . $date_info[0]['datestamp'] . "'";

        $sql = <<<EOF
        SELECT a.info_id, a.`scan_date` `datestamp`, a.pv scan_pv, a.uv scan_uv, a.`no_login_pv` scan_no_login_pv, 
        b.`pv` comment_pv, b.`uv` comment_uv, c.`pv` share_pv, c.uv share_uv 
        FROM t_scan_flow_daily a 
        LEFT JOIN t_info_comment_daily b ON a.`info_id` = b.`info_id` AND a.`scan_date` = b.`comment_date`
        LEFT JOIN t_share_info_daily c ON a.`scan_date` = c.`share_date` AND a.`info_id` = c.`info_id` {$time_str};
EOF;
        $info_daily = queryByNoModel('t_scan_flow_daily', '', $this->stat_config, $sql);
        if($info_daily === false)
            return false;
        
        for($i = 0; $i < count($info_daily); $i++)
        {
            $insert_data = array();
            $insert_data['info_id'] = $info_daily[$i]['info_id'];
            $insert_data['datestamp'] = $info_daily[$i]['datestamp'];
            $insert_data['scan_pv'] = $info_daily[$i]['scan_pv'];
            $insert_data['scan_uv'] = $info_daily[$i]['scan_uv'];
            $insert_data['scan_no_login_pv'] = $info_daily[$i]['scan_no_login_pv'];
            $insert_data['comment_pv'] = $info_daily[$i]['comment_pv'];
            $insert_data['comment_uv'] = $info_daily[$i]['comment_uv'];
            $insert_data['share_pv'] = $info_daily[$i]['share_pv'];
            $insert_data['share_uv'] = $info_daily[$i]['share_uv'];
            $insert_re = insertByNoModel('t_info_daily', '', $this->stat_config, $insert_data); 
            if($insert_re === false)
                return false;
        }
        ## 获取文章的发布时间
        $sql = "SELECT DISTINCT info_id FROM t_info_daily WHERE pub_time IS NULL";
        $id_list = queryByNoModel('t_info_daily', '', $this->stat_config, $sql); 
        if($id_list === false)
            return false;
        if(count($id_list) > 0)
        {
            $in_str = "(";
            for($i = 0; $i < count($id_list); $i++)
                if($i != count($id_list) - 1)
                    $in_str .= $id_list . ", ";
                else
                    $in_str .= $id_list . ")";
            $sql = "SELECT info_id, create_time FROM t_info_summary WHERE info_id IN" . $in_str;
            $list_result = $this->query($sql);
            if($list_result === false)
                return false;

            ## 更新t_info_daily表中文章的发布时间
            $obj_mod = M('t_info_daily', '', $this->stat_config);
            $obj_mod->execute("SET NAMES utf8");
            for($i = 0; $i < count($list_result); $i++)
            {
                // $update_sql = "UPDATE t_info_daily SET pub_time = '" . $list_result[$i]['create_time'] . "' WHERE info_id = " . $list_result[$i]['info_id'];
                $condition['info_id'] = $list_result[$i]['info_id'];
                $data['pub_time'] = $list_result[$i]['create_time'];
                $result = $obj_mod->where($condition)->save($data);
                if($result === false)
                    return false;
            }
        }
        return true;
    }

}
