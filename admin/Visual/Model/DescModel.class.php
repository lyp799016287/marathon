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
        SELECT b.{$field} field_name, COUNT(DISTINCT a.user_uid) part_num FROM 
        (SELECT user_uid, MAX(login_time) latest_login_time FROM t_user_device_flow GROUP BY user_uid) a 
        LEFT JOIN t_user_device_flow b ON a.user_uid = b.user_uid AND a.latest_login_time = b.login_time
        GROUP BY b.{$field}
        ORDER BY COUNT(DISTINCT a.user_uid) DESC
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

    public function calNewRetain($bgn, $end)
    {
        $bgn = date('Y-m-d', strtotime($bgn));
        $end = date('Y-m-d', strtotime($end));
        $sql = <<<EOF
        SELECT a.*, b.new_user FROM 
        (SELECT register_date, retain_1, retain_2, retain_3, retain_4, retain_5, retain_6, retain_7, retain_14, retain_30 
        FROM t_user_retain WHERE register_date >= '{$bgn}' AND register_date <= '{$end}') a 
        LEFT JOIN t_user_summary b ON a.register_date = b.datestamp 
        ORDER BY a.register_date
EOF;
        return $this->query($sql);
    }

    public function calActiveRetain($bgn, $end)
    {
        $bgn = date('Y-m-d', strtotime($bgn));
        $end = date('Y-m-d', strtotime($end));
        $sql = <<<EOF
        SELECT datestamp, active_user, retain_1, retain_2, retain_3, retain_4, retain_5, retain_6, retain_7, retain_14, retain_30 
        FROM t_user_active_retain WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}'
        ORDER BY datestamp 
EOF;
        return $this->query($sql);
    }

    public function getChannelByDate($date)
    {
        $sql = "SELECT channel, new_user, active_user, open_times FROM t_user_channel WHERE datestamp = '" . $date . "'";
        $re = $this->stdQuery($sql);
        if($re === false)
            return false;
        ## 当天各个维度的总用户量
        $sql_sum = "SELECT SUM(new_user) all_new, SUM(active_user) all_active, SUM(open_times) all_open FROM t_user_channel WHERE datestamp = '" . $date . "'";
        $re_sum = $this->query($sql_sum);
        if($re_sum === false)
            return false;
        $all_new = $re_sum[0]['all_new'];
        $all_active = $re_sum[0]['all_active'];
        $all_open = $re_sum[0]['all_open'];
        ## 各个渠道的累计用户
        $sql_cumu = <<<EOF
            SELECT channel, SUM(new_user) cnt 
            FROM t_user_channel 
            WHERE datestamp <= '{$date}' AND channel IN (SELECT channel FROM t_user_channel WHERE datestamp = '{$date}')
            GROUP BY channel 
EOF;
        $re_cumu = $this->stdQuery($sql_cumu);
        if($re_cumu === false)
            return false;

        for($i = 0; $i < count($re); $i++)
        {
            $re[$i]['new_per'] = round((float)$re[$i]['new_user'] / $all_new * 100, 2);
            $re[$i]['active_per'] = round((float)$re[$i]['active_user'] / $all_active * 100, 2);
            $re[$i]['cumu_user'] = 0;
            for($j = 0; $j < count($re_cumu); $j++)
                if($re_cumu[$j]['channel'] == $re[$i]['channel'])
                {
                    $re[$i]['cumu_user'] = $re_cumu[$j]['cnt'];
                    break;
                }
        }
        return $re;

    }

    private function stdQuery($sql)
    {
        $tmp_sql = "SET NAMES utf8";
        $this->execute($tmp_sql);
        return $this->query($sql);
    }


    public function getGraphData($bgn, $end, $type)
    {
        $field = '';
        if($type == 1)
            $field = "new_user";
        elseif($type == 2)
            $field = "active_user";
        elseif($type == 3)
            $field = "open_times";

        if(empty($field))
            return false;
        $sql = <<<EOF
            SELECT datestamp, channel, {$field} cnt FROM t_user_channel 
            WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}'
EOF;
        // return $this->stdQuery($sql);
        $data = $this->stdQuery($sql);
        $sql_date = <<<EOF
            SELECT DISTINCT datestamp FROM t_user_channel
            WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}'
EOF;
        $re_date = $this->stdQuery($sql_date);
        $sql_channel = <<<EOF
            SELECT DISTINCT channel FROM t_user_channel
            WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}'
EOF;
        $re_channel = $this->stdQuery($sql_channel);
        if($data === false || $re_date === false || $re_channel === false)
            return false;
        return array('data'=>$data, 'datestamp'=>$re_date, 'channel'=>$re_channel);
    }

    public function getSdkByDate($date)
    {
        $sql = "SELECT sys_version, new_user, active_user, open_times FROM t_user_sys_version WHERE datestamp = '" . $date . "'";
        $re = $this->stdQuery($sql);
        if($re === false)
            return false;
        ## 各个维度的总用户量
        $sql_sum = "SELECT SUM(new_user) all_new, SUM(active_user) all_active, SUM(open_times) all_open FROM t_user_sys_version WHERE datestamp = '" . $date . "'";
        $re_sum = $this->stdQuery($sql_sum);
        if($re_sum === false)
            return false;
        $all_new = $re_sum[0]['all_new'];
        $all_active = $re_sum[0]['all_active'];
        $all_open = $re_sum[0]['all_open'];
        ## 计算百分比
        for($i = 0; $i < count($re); $i++)
        {
            $re[$i]['new_per'] = round((float)$re[$i]['new_user'] / $all_new * 100, 2);
            $re[$i]['active_per'] = round((float)$re[$i]['active_user'] / $all_active * 100, 2);
            $re[$i]['open_per'] = round((float)$re[$i]['open_times'] / $all_open * 100, 2);
        }
        return $re;
    }

    public function getSdkGraph($bgn, $end, $type)
    {
        $field = '';
        if($type == 1)
            $field = 'new_user';
        elseif($type == 2)
            $field = 'active_user';
        elseif($type == 3)
            $field = "open_times";
        else
            return false;
        $sql = <<<EOF
            SELECT app_version, SUM({$field}) all_cnt FROM t_user_app_version 
            WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}' 
            GROUP BY app_version 
EOF;
        return $this->stdQuery($sql);
    }

    public function getVersionByDate($date)
    {
        $sql = "SELECT app_version, new_user, update_user, active_user, open_times FROM t_user_app_version WHERE datestamp = '" . $date . "'";
        $re = $this->stdQuery($sql);
        if($re === false)
            return false;
        ## 各个维度的总用户量
        $sql_all = "SELECT SUM(new_user) all_new, SUM(active_user) all_active, SUM(open_times) all_open FROM t_user_app_version WHERE datestamp = '" . $date . "'";
        $re_all = $this->stdQuery($sql_all);
        if($re_all === false)
            return false;
        $new_all = $re_all[0]['all_new'];
        // $update_all = $re_all[0]['all_update'];
        $active_all = $re_all[0]['all_active'];
        $open_all = $re_all[0]['all_open'];

        ## 各个版本的累计用户
        $sql_cumu = <<<EOF
        SELECT app_version, SUM(new_user) all_new 
        FROM t_user_app_version 
        WHERE datestamp <= '{$date}' AND app_version IN 
                        (SELECT DISTINCT app_version FROM t_user_app_version WHERE datestamp = '{$date}')
        GROUP BY app_version 
EOF;
        $re_cumu = $this->stdQuery($sql_cumu);
        if($re_cumu === false)
            return false;
        ## 各个版本的新增用户
//         $sql_new = <<<EOF
//             SELECT b.app_version, COUNT(b.user_uid) cnt FROM 
//             (SELECT * FROM t_user_device_flow WHERE login_date = '{$date}') b
//             LEFT JOIN 
//             (SELECT user_uid, MAX(app_version) max_version_before
//             FROM t_user_device_flow 
//             WHERE user_uid IN (SELECT user_uid FROM t_user_device_flow WHERE login_date = '{$date}') AND login_date < '{$date}'
//             GROUP BY user_uid ) a
//             ON a.user_uid = b.user_uid 
//             WHERE a.user_uid IS NULL 
//             GROUP BY b.app_version 
// EOF;
//         $re_new = $this->stdQuery($sql_new);
//         if($re_new === false)
//             return false;
//         ## 各个版本的升级用户
//         $sql_update = <<<EOF
//             SELECT app_version, COUNT(a.user_uid) cnt FROM 
//             (SELECT user_uid, MAX(app_version) max_version_before
//             FROM t_user_device_flow 
//             WHERE user_uid IN (SELECT user_uid FROM t_user_device_flow WHERE login_date = '{$date}') AND login_date < '{$date}'
//             GROUP BY user_uid) a
//             LEFT JOIN 
//             (SELECT * FROM t_user_device_flow WHERE login_date = '{$date}') b
//             ON a.user_uid = b.user_uid 
//             WHERE max_version_before < app_version
//             GROUP BY app_version 
// EOF;
//         $re_update = $this->stdQuery($sql_update);
//         if($re_update === false)
//             return false;

        for($i = 0; $i < count($re); $i++)
        {
            $version = $re[$i]['app_version'];
            $re[$i]['new_per'] = round((float)$re[$i]['new_user'] / $new_all * 100, 2);
            $re[$i]['active_per'] = round((float)$re[$i]['active_user'] / $active_all * 100, 2);
            $re[$i]['open_per'] = round((float)$re[$i]['open_times'] / $open_all * 100, 2);

            // ## 新增用户
            // for($j = 0; $j < count($re_new); $j++)
            //     if($re_new[$j]['app_version'] == $version)
            //     {
            //         $re[$i]['new_user'] = $re_new[$j]['cnt'];
            //         $new_all += $re_new[$j]['cnt'];
            //         break;
            //     }
            // ## 更新用户
            // for($k = 0; $k < count($re_update); $k++)
            //     if($re_update[$k]['app_version'] == $version)
            //     {
            //         $re[$i]['update_user'] = $re_update[$k]['cnt'];
            //         break;
            //     }
            for($j = 0; $j < count($re_cumu); $j++)
                if($re_cumu[$j]['app_version'] == $version)
                {
                    $re[$i]['cumu_user'] = $re_cumu[$j]['all_new'];
                }
        }

        // ## 计算各个版本的新增用户占比
        // for($i = 0; $i < count($re); $i++)
        //     $re[$i]['new_per'] = ($re[$i]['new_per'] == 0) ? 0 : round((float)$re[$i]['new_user'] / $all_new * 100, 2);

        return $re;
    }

    public function getVersionLine($bgn, $end, $type)
    {
        $field = '';
        if($type == 1) ## 新用户
            $field = "new_user";
        elseif ($type == 2) ## 更新用户
            $field = "update_user";
        elseif($type == 3) ## 活跃用户
            $field = 'active_user';
        elseif($type == 4) ## 启动次数
            $field = 'open_times';
        else
            return false;
        $sql = <<<EOF
        SELECT datestamp, app_version, {$field} 
        FROM t_user_app_version 
        WHERE datastamp >= '{$bgn}' AND datestamp <= '{$end}'
EOF;
        return $this->stdQuery($sql);
    }


    public function retainSummary($bgn, $end)
    {
        $sql = <<<EOF
        SELECT a.*, b.retain_1 retain_user FROM 
        (SELECT datestamp, new_user FROM t_user_summary WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}') a 
        LEFT JOIN t_user_retain b ON a.datestamp = b.register_date
EOF;
        // var_dump($sql); exit;
        return $this->query($sql);
    }

    public function retainTable($data, $bgn, $end)
    {
        $start = ($data['current_page'] - 1) * $data['page_size'];
        $len = $data['page_size'] - 1;
        $limit = ' LIMIT '.$start.', '.$len;
        $order = '';
        if(isset($data['sort_name']) && !empty($data['sort_name']))
            $order .= ' ORDER BY '.$data['sort_name'];
        else
            $order .= ' ORDER BY datestamp';
        if(isset($data['sort_order']) && !empty($data['sort_order']))
            $order .= ' '.$data['sort_order'];
        else
            $order .= ' DESC';

        $sql = <<<EOF
        SELECT a.*, b.retain_1 retain_user FROM 
        (SELECT datestamp, new_user FROM t_user_summary WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}') a 
        LEFT JOIN t_user_retain b ON a.datestamp = b.register_date {$order} {$limit}
EOF;
        $re_data = $this->query($sql);
        if($re_data === false)
            return false;

        $sql_len = <<<EOF
        SELECT COUNT(DISTINCT a.datestamp) len FROM 
        (SELECT datestamp, new_user FROM t_user_summary WHERE datestamp >= '{$bgn}' AND datestamp <= '{$end}') a 
        LEFT JOIN t_user_retain b ON a.datestamp = b.register_date
EOF;
        $re_len = $this->query($sql_len);
        return array('data'=>$re_data, 'len'=>$re_len[0]['len']);
    }


}
