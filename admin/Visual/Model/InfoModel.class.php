<?php
namespace Visual\Model;
use Think\Model;

class InfoModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_info_daily';

    public function _initialize()
    {
        $this->imed_config = C('DB_IMED');
    }


    public function topSummary()
    {
        $yesterday = date('Y-m-d', strtotime("-1 day"));
//         $sql = <<<EOF
//         SELECT a.info_id, b.title, a.score
//         FROM (SELECT * FROM t_info_daily WHERE datestamp = '{$yesterday}') a LEFT JOIN imed.`t_info_summary` b ON a.info_id = b.info_id
//         ORDER BY a.score DESC 
//         LIMIT 5
// EOF;
        $sql = <<<EOF
        SELECT info_id, title, score
        FROM t_info_daily
        WHERE datestamp = '{$yesterday}'
        ORDER BY a.score DESC LIMIT 5
EOF;
        $re = $this->query($sql);
        return $re;
    }


    public function detailSummary()
    {
        $yesterday = date('Y-m-d', strtotime("-1 day"));
//         $sql = <<<EOF
//         SELECT a.info_id, b.title, a.pub_time, a.scan_pv, a.scan_uv, a.scan_no_login_pv, a.comment_pv, a.comment_uv, a.share_pv, a.share_uv, a.score
//         FROM (SELECT * FROM t_info_daily WHERE datestamp = '{$yesterday}') a LEFT JOIN imed.`t_info_summary` b ON a.info_id = b.info_id
//         ORDER BY a.scan_pv DESC
// EOF;
        $sql = <<<EOF
        SELECT info_id, title, pub_time, scan_pv, scan_uv, scan_no_login_pv, comment_pv, comment_uv, share_pv, share_uv, score
        FROM t_info_daily WHERE datestamp = '{$yesterday}'
        ORDER BY scan_pv DESC
EOF;
        $re = $this->query($sql);
        return $re;
    }

    public function accumulateInfo()
    {
        $maxTime = "SELECT MAX(modify_time) max_time FROM t_info_accumulate";
        $re_time = $this->query($maxTime);
        if($re_time === false)
            return array('code'=>-1, 'message'=>'查询错误');
        $time_clause = " 1";
        # $max_time 表t_info_accumulate中需要更新的时间戳
        if(count($re_time) > 0)
        {
            $max_time = $re_time[0]['max_time'];
            $time_clause = " create_time > '" . $re_time[0]['max_time'] . "'";
        }
        else
        {
            $max_time = date("Y-m-d H:i:s", now());
        }
            
        ## 资讯浏览
        $scanInfo = <<<EOF
        SELECT a.info_id, a.scan_pv, a.scan_uv, b.scan_no_login_pv FROM 
        (SELECT info_id, COUNT(id) scan_pv, COUNT(DISTINCT uid) scan_uv FROM t_scan_flow WHERE {$time_clause} GROUP BY info_id) a LEFT JOIN 
        (SELECT info_id, COUNT(id) scan_no_login_pv FROM t_scan_flow WHERE uid = 0 AND {$time_clause} GROUP BY info_id) b 
        ON a.info_id = b.info_id 
EOF;
        $scan = $this->query($scanInfo);
        ## 资讯分享
        $shareInfo = <<<EOF
        SELECT target_id info_id, COUNT(id) share_pv, COUNT(DISTINCT user_id) share_uv FROM t_share 
        WHERE `type` = 2 AND {$time_clause} GROUP BY target_id
EOF;
        $share = queryByNoModel('t_share', '', $this->imed_config, $shareInfo);
        ## 资讯评论
        $time_clause = " 1";
        if(count($re_time) > 0)
            $time_clause = " `time` > '" . $re_time[0]['max_time'] . "'";

        $commentInfo = <<<EOF
        SELECT info_id, COUNT(DISTINCT user_id) comment_uv, COUNT(comment_id) comment_pv 
        FROM imed.t_info_comment WHERE `status` = 1 AND {$time_clause} GROUP BY info_id
EOF;
        $comment = queryByNoModel('t_info_comment', '', $this->imed_config, $commentInfo);

        if($scan === false || $comment === false || $share === false)
            return array('code'=>-2, 'message'=>'查询错误');

        $info = $this->mergeInfo($scan, $share, $comment);
        if($info['code'] < 0)
            return $info;
        ## 将数据更新到表t_info_accumulate
        $re = $this->insertOrUpdate($info, $max_time);
        return $re;
    }

    private function mergeInfo($scan, $share, $comment)
    {
        if(count($scan) == 0)
            return array('code'=>0, 'message'=>'');

        ## 获取文章的标题和发布时间
        $titleSql = "SELECT info_id, title, create_time pub_time FROM t_info_summary WHERE info_id IN " . $id_clause;
        $extraInfo = queryByNoModel('t_info_summary', '', $this->imed_config, $titleSql);
        if($extraInfo === false)
            return array('code'=>-3, 'message'=>'查询错误');

        $info = array();
        $id_clause = "(";
        for($i = 0; $i < count($scan); $i++)
        {
            if($i != count($scan) - 1)
                $id_clause .= $scan[$i]['info_id'] . ", ";
            else
                $id_clause .= $scan[$i]['info_id'];
            $info[$i]['info_id'] = $scan[$i]['info_id'];
            $info[$i]['scan_pv'] = $scan[$i]['scan_pv'];
            $info[$i]['scan_uv'] = $scan[$i]['scan_uv'];
            $info[$i]['scan_no_login_pv'] = $scan[$i]['scan_uv'];
            $info[$i]['share_uv'] = 0;
            $info[$i]['share_pv'] = 0;
            $info[$i]['comment_uv'] = 0;
            $info[$i]['comment_pv'] = 0;
            $info[$i]['title'] = '';
            $info[$i]['pub_time'] = '';
            ## 填充title pub_time字段
            for($j = 0; $j < count($extraInfo); $j++)
            {
                if($scan[$i]['info_id'] == $extraInfo[$j]['info_id'])
                {
                    $info[$i]['title'] = $extraInfo[$j]['title'];
                    $info[$i]['pub_time'] = $extraInfo[$j]['pub_time'];
                    break;
                }
            }
        }
        $id_clause .= ")";

        for($i = 0; $i < count($info); $i++)
        {
            $info_id = $info[$i]['info_id'];
            for($j = 0; $j < count($share); $j++)
                if($share[$j]['info_id'] == $info_id)
                {
                    $info[$i]['share_pv'] = $share[$j]['share_pv'];
                    $info[$i]['share_uv'] = $share[$j]['share_uv'];
                    break;
                }
            for($k = 0; $k < count($comment); $k++)
                if($comment[$k]['info_id'] == $info_id)
                {
                    $info[$i]['comment_pv'] = $share[$j]['comment_pv'];
                    $info[$i]['comment_uv'] = $share[$j]['comment_uv'];
                    break;
                } 
        }

        return array('code'=>1, 'message'=>'', 'data'=>$info);

    }

    private function insertOrUpdate($info, $max_time)
    {
        $error_clause = '';
        for($i = 0; $i < count($info); $i++)
        {
            $info_id = $info[$i]['info_id'];
            $sql = "SELECT * FROM t_info_accumulate WHERE info_id = " . $info_id;
            $re_exist = $this->query($sql);
            if($re_exist === false)
                return array('code'=>-4, 'message'=>'查询错误');
            $title = $info[$i]['title'];
            $pub_time = $info[$i]['pub_time'];
            $scan_pv = $info[$i]['scan_pv'];
            $scan_uv = $info[$i]['scan_uv'];
            $scan_no_login_pv = $info[$i]['scan_no_login_pv'];
            $comment_pv = $info[$i]['comment_pv'];
            $comment_uv = $info[$i]['comment_uv'];
            $share_pv = $info[$i]['share_pv'];
            $share_uv = $info[$i]['share_uv'];
            ## insert 
            if(count($re_exist) == 0)
            {
                $sql = <<<EOF
                INSERT INTO t_info_accumulate(info_id, title, scan_pv, scan_uv, scan_no_login_pv, comment_pv, comment_uv, 
                    share_uv, share_pv, pub_time, modify_time)
                VALUES($info_id, '{$title}', {$scan_pv}, {$share_uv}, {$scan_no_login_pv}, {$comment_pv}, 
                    {$comment_uv}, {$share_uv}, {$share_pv}, '{$pub_time}', '{$max_time}')
EOF;
                $re = $this->execute($sql);
                if($re === false)
                    $error_clause .= $info_id . ", ";
            }
            elseif(count($re_exist) == 1) ## update
            {
                $sql = <<<EOF
                UPDATE t_info_accumulate
                SET scan_pv = scan_pv + {$scan_pv},
                    scan_uv = scan_uv + {$scan_uv},
                    scan_no_login_pv = scan_no_login_pv + {$scan_no_login_pv},
                    comment_pv = comment_pv + {$comment_pv},
                    comment_uv = comment_uv + {$comment_uv},
                    share_pv = share_pv + {$share_pv},
                    share_uv = share_uv + {$share_uv},
                    modify_time = '{$max_time}'
                WHERE info_id = {$info_id}
EOF;
                $re = $this->execute($sql);
                if($re === false)
                    $error_clause .= $info_id . ", ";
            }

        }
        if($error_clause == '')
            return array('code'=>1, 'message'=>'');
        else
            return array('code'=>-5, 'message'=>$error_clause);
    }

    public function accumulateResult()
    {
        $sql = "SELECT info_id, title, score FROM t_info_accumulate ORDER BY score DESC LIMIT 5";
        $top = $this->query($sql);

        $sql = <<<EOF
        SELECT info_id, title, scan_pv, scan_uv, scan_no_login_pv, comment_pv, comment_uv, share_pv, share_uv, score, pub_time
        FROM t_info_accumulate
        OEDER BY scan_pv DESC
EOF;
        $detail = $this->query($sql);

        if($top === false || $detail === false)
            return false;
        else
            return array('top'=>$top, 'detail'=>$detail);
    }

}
