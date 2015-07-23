<?php
namespace Visual\Model;
use Think\Model;

class InfoModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_info_daily';


    public function topSummary()
    {
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $sql = <<<EOF
        SELECT a.info_id, b.title, a.score
        FROM (SELECT * FROM t_info_daily WHERE datestamp = '{$yesterday}') a LEFT JOIN imed.`t_info_summary` b ON a.info_id = b.info_id
        ORDER BY a.score DESC 
        LIMIT 5
EOF;
        $re = $this->query($sql);
        return $re;
    }


    public function detailSummary()
    {
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $sql = <<<EOF
        SELECT a.info_id, b.title, a.pub_time, a.scan_pv, a.scan_uv, a.scan_no_login_pv, a.comment_pv, a.comment_uv, a.share_pv, a.share_uv, a.score
        FROM (SELECT * FROM t_info_daily WHERE datestamp = '{$yesterday}') a LEFT JOIN imed.`t_info_summary` b ON a.info_id = b.info_id
        ORDER BY a.scan_pv DESC
EOF;
        $re = $this->query($sql);
        return $re;
    }

}
