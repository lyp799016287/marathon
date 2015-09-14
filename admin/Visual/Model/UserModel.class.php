<?php
namespace Visual\Model;
use Think\Model;

class UserModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_summary';

    public function _initialize()
    {
        $this->imed_config = C('DB_IMED');
        $this->topNum = 10; ## top显示的条数
    }


    public function getLatest()
    {
        $sql = "SELECT datestamp, cumulation_user, new_user, login_user, active_user FROM t_user_summary ORDER BY datestamp DESC LIMIT 1";
        $re = $this->query($sql);
        return $re;
    }


    public function getLatestCumu($idx, $type)
    {
        if($type == 1)
            $date_bgn = date('Y-m-d', strtotime("-7 days"));
        elseif($type == 2)
            $date_bgn = date('Y-m-d', strtotime("-7 weeks"));
        elseif($type == 3)
            $date_bgn = date('Y-m-d', strtotime("-7 months"));
        else
            return false;
        if($idx == 2)
            $sql = "SELECT datestamp, cumulation_user, new_user, login_user, active_user FROM t_user_summary WHERE datestamp >= '" . $date_bgn . "' ORDER BY datestamp";
        elseif($idx == 1)
            $sql = "SELECT datestamp, cumulation_user FROM t_user_summary WHERE datestamp >= '" . $date_bgn . "' ORDER BY datestamp";
        // var_dump($sql);
        $re = $this->query($sql);
        if(empty($re))
            return $re;
        else
        {
            if($type == 1)
                return $re;
            $date_end = $re[0]['datestamp']; ## 最小的日期
            $date_bgn = $re[count($re) - 1]['datestamp']; ## 最大的日期
            $return_ary = array();
            while($date_bgn > $date_end)
            {
                $idx = $this->getIdx($re, $date_bgn);
                if($idx != -1)
                {
                    $return_ary[] = $re[$idx];
                    if($type == 2)
                        $date_bgn = date('Y-m-d', strtotime("-1 week", strtotime($date_bgn)));
                    elseif($type == 3)
                        $date_bgn = date('Y-m-d', strtotime("-1 month", strtotime($date_bgn)));
                }
                else
                    return false;
            }
            $return_ary = array_reverse($return_ary, true);
            return $return_ary;
        }
        
    }


    private function getIdx($ary, $date_bgn)
    {
        for($i = 0; $i < count($ary); $i++)
        {
            if($ary[$i]['datestamp'] == $date_bgn)
                return $i;
        }
        return -1;
    }


    public function getHourly($data)
    {
        $sql = "SELECT MAX(SUBSTRING(CAST(time_stamp AS CHAR(20)), 1, 10)) max_date FROM t_user_summary_by_hour";
        $date_re = $this->query($sql);
        if($date_re === false)
            return false;
        elseif(count($date_re) == 0)
            return array();
        $bgn_time = $date_re[0]['max_date'] . ' 00:00:00';

        $start = ($data['current_page'] - 1) * $data['page_size'];
        $len = $data['page_size'] - 1;
        $limit = ' LIMIT '.$start.', '.$len;
        $order = '';
        if(isset($data['sort_name']) && !empty($data['sort_name']))
            $order .= ' ORDER BY '.$data['sort_name'];
        else
            $order .= ' ORDER BY time_stamp';
        if(isset($data['sort_order']) && !empty($data['sort_order']))
            $order .= ' '.$data['sort_order'];
        else
            $order .= ' DESC';

        $select_sql = <<<EOF
        SELECT SUBSTRING(CAST(time_stamp AS CHAR(20)), 12, 5) AS hour_stamp, 
        new_user, active_user, open_times 
        FROM t_user_summary_by_hour 
        WHERE time_stamp >= '{$bgn_time}'
        {$order}{$limit}
EOF;
        $data = $this->query($select_sql);
        if($data === false)
            return false;
        $count_sql = <<<EOF
        SELECT COUNT(*) total_record
        FROM t_user_summary_by_hour 
        WHERE time_stamp >= '{$bgn_time}'
EOF;
        $total = $this->query($count_sql);
        if($total === false)
            return false;
        return array('data'=>$data, 'total'=>$total[0]['total_record']);
    }

    public function getKeyData()
    {
        $return_ary = array();
        $sql = "SELECT time_stamp, cumulation_user, new_user FROM t_user_summary_by_hour ORDER BY time_stamp DESC LIMIT 1";
        $re = $this->query($sql);
        if($re === false)
            return false;
        else
        {
            $return_ary['time_stamp'] = date('Y-m-d H:i:s', strtotime("+1 hour", strtotime($re[0]['time_stamp'])));
            $return_ary['cumulation_user'] = $re[0]['cumulation_user'];
            $return_ary['new_user'] = $re[0]['new_user'];
        }

        $sql = "SELECT CASE WHEN SUM(active_user) > 0 THEN SUM(active_user) ELSE 0 END totalActive, SUM(open_times) totalOpen, SUM(login_user) totalLogin FROM t_user_summary_by_hour";
        $re = $this->query($sql);
        // var_dump($re); exit;
        if($re === false)
            return false;
        else
        {
            $return_ary['totalActive'] = $re[0]['totalactive'];
            $return_ary['totalOpen'] = $re[0]['totalopen'];
            $return_ary['totalLogin'] = $re[0]['totallogin'];
        }
            

        $sql = "SELECT active_user, open_times FROM t_user_summary_by_hour ORDER BY time_stamp DESC LIMIT 1";
        $re = $this->query($sql);
        if($re === false)
            return falser;
        else
        {
            $return_ary['active_user'] = $re[0]['active_user'];
            $return_ary['open_times'] = $re[0]['open_times'];
        }

        ## 周活跃用户数
        $sql = "SELECT MAX(time_stamp) time_stamp FROM t_user_summary_by_hour";
        $re = $this->query($sql);
        if($re === false)
            return false;
        elseif(count($re) == 0)
            $return_ary['weekly_active'] = 0;
        else
        {
            $re = $this->getWeeklyActive($re[0]['time_stamp']);
            if($re === false)
                return false;
            else
                $return_ary['weekly_active'] = $re;
        }

        ## 月留存率
        // $this->monthlyRetain();

        // var_dump($return_ary); exit;
        return $return_ary;
    }

    ## 周活跃用户
    private function getWeeklyActive($max_stamp)
    {
        $max_date = substr($max_stamp, 0, 10);
        $max_time = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($max_stamp)));
        // var_dump($max_date);
        // var_dump($max_time);
        $bgn_time = date("Y-m-d H:i:s", strtotime("-6 days", strtotime($max_date)));
        ## 7天内登录的用户数
        $sql = <<<EOF
        SELECT COUNT(DISTINCT user_uid) login_user 
        FROM t_login_flow 
        WHERE `status` IN(1, 2) AND create_time >= '{$bgn_time}' AND create_time < '{$max_time}' 
EOF;
        // $re = $this->query($sql);
        $re = queryByNoModel('t_login_flow', '', $this->imed_config, $sql);
        if($re === false)
            return false;
        $login_user = $re[0]['login_user'];
        $sql = <<<EOF
        SELECT COUNT(id) new_user
        FROM t_user_info 
        WHERE `status` = 1 AND create_time >= '{$bgn_time}' AND create_time < '{$max_time}'
EOF;
        // $re = $this->query($sql);
        $re = queryByNoModel('t_user_info', '', $this->imed_config, $sql);
        if($re === false)
            return false;
        $new_user = $re[0]['new_user'];
        return ($login_user - $new_user > 0) ? ($login_user - $new_user) : 0;

    }

    private function monthlyRetain()
    {

    }

    public function userInfoByDay($type, $bgn_date, $end_date)
    {       
        if($type > 0)
        {
            $limit = "";
            switch($type)
            {
                case 1: ## 7天
                    $limit = "LIMIT 7";
                    break;
                case 2: ## 14天
                    $limit = "LIMIT 14";
                    break;
                case 3: ## 30天
                    $limit = "LIMIT 30";
                    break;
                default: 
                    return false; ## 参数错误
            }
            $sql = "SELECT datestamp, new_user, active_user, open_times FROM t_user_summary ORDER BY datestamp DESC " . $limit;
            $result = $this->query($sql);
            return $result;
        }
        else
        {
            $sql = <<<EOF
            SELECT datestamp, new_user, active_user, open_times 
            FROM t_user_summary 
            WHERE datestamp >= '{$bgn_date}' AND datestamp <= '{$end_date}'
            ORDER BY datestamp DESC 
EOF;
            return $this->query($sql);
        }
    }


    public function getDailyBasic($data, $type, $bgn_date, $end_date)
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
        
        if($type > 0)
        {
            ## 计算bgn_date   end_date
            $sql = "SELECT MAX(datestamp) datestamp FROM t_user_summary";
            $max_date = $this->query($sql);
            if($max_date === false)
                return false;
            $end_date = $max_date[0]['datestamp'];

            switch($type)
            {
                case 1:
                    $bgn_date = date("Y-m-d", strtotime("-6 days", strtotime($end_date)));
                    break;
                case 2:
                    $bgn_date = date("Y-m-d", strtotime("-13 days", strtotime($end_date)));
                    break;
                case 3:
                    $bgn_date = date("Y-m-d", strtotime("-29 days", strtotime($end_date)));
                    break;
                default:
                    return false;
            }
        }
        $sql = <<<EOF
        SELECT datestamp, new_user, active_user, open_times 
        FROM t_user_summary 
        WHERE datestamp >= '{$bgn_date}' AND datestamp <= '{$end_date}'
        {$order}{$limit}
EOF;
        // var_dump($sql); 
        $result = $this->query($sql);
        if($result === false)
            return false;
        $sql = "SELECT COUNT(*) total_record FROM t_user_summary WHERE datestamp >= '{$bgn_date}' AND datestamp <= '{$end_date}'";
        $len = $this->query($sql);
        // var_dump($sql);
        if($len === false)
            return false;
        // var_dump($result);
        // var_dump($len);
        return array('data'=>$result, 'total'=>$len[0]['total_record']);

    }

}
