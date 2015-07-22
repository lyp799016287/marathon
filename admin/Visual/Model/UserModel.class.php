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
        // var_dump($date_bgn);
        $sql = "SELECT datestamp, cumulation_user FROM t_user_summary WHERE datestamp >= '" . $date_bgn . "' ORDER BY datestamp DESC ";
        $re = $this->query($sql);
        return $re;
    }

}
