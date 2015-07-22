<?php
namespace Visual\Model;
use Think\Model;

class UserModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_user_summary';


    public function getLatestCumu($type)
    {
        if($type == 1)
            $date_bgn = date('Y-m-d', strtotime("-7 days"));
        elseif($type == 2)
            $date_bgn = date('Y-m-d', strtotime("-7 weeks"));
        elseif($type == 3)
            $date_bgn = date('Y-m-d', strtotime("-7 months"));
        else
            return false;
        $sql = "SELECT datestamp, cumulation_user FROM t_user_summary WHERE datestamp >= '" . $date_bgn . "' ORDER BY datestamp DESC ";
        $re = $this->query($sql);
        if(empty($re))
            return $re;
        else
        {
            if($type == 1)
                return $re;
            $date_bgn = $re[0]['datestamp'];
            $date_end = $re[count($re) - 1]['datestamp'];

            $return_ary = array();
            $j = 0;
            
            while($date_bgn < $date_end)
            {
                $idx = $this->getIdx($re, $date_bgn);
                if($idx != -1)
                {
                    $return_ary[$j] = $re[$idx];
                    if($type == 2)
                        $date_bgn = date('Y-m-d', strtotime("+1 week", strtotime($re[$i]['datestamp'])));
                    else
                        $date_bgn = date('Y-m-d', strtotime("+1 month", strtotime($re[$i]['datestamp'])));
                    $j++;
                }
                else
                    return false;
            }
            // var_dump($return_ary);
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
