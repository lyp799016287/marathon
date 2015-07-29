<?php
namespace Visual\Model;
use Think\Model;

class UserModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_summary';


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
            $return_ary = array_reverse($return_ary);
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

}
