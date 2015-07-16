<?php
namespace Stat\Model;
use Think\Model;

class SecretModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_secret';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function secretDaily()
    {
        $now_date = date('Y-m-d', time());
        $sql = "SELECT MAX(datestamp) `date` FROM t_secret_daily";
        $date_re = queryByNoModel('t_secret_daily', '', $this->stat_config, $sql);
        if($date_re === false)
            return array('code'=>-7, 'message'=>'查询操作失败');

        $max_date = $date_re[0]['date'];
        ## 实时更新今天的数据 update
        if($max_date == $now_date)
        {
            $next_date = date('Y-m-d', strtotime($max_date) + 86400);
            $update_re = $this->getRecord($max_date, $next_date);
            if($update_re['code'] < 0)
                return $update_re;
            $update_data = $update_re['data'];
            $update_data['modify_time'] = date('Y-m-d H:i:s', time());
            // var_dump($update_data);exit;
            $condition['datestamp'] = $max_date;
            $update_re = $this->updateTable('t_secret_daily', $update_data, $condition);
            if($update_re === false)
                return array('code'=>-1, 'message'=>"更新实时数据失败");
        }
        ## 添加未统计的日期记录 insert
        else
        {
            if(is_null($max_date))
            {
                // var_dump($max_date); exit;
                $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) `date` FROM t_secret";
                $date = $this->query($sql);
                if($date === false)
                    return array('code'=>-2, 'message'=>"执行sql失败：" . $sql);
                $max_date = $date[0]['date'];
            }
            
            while($max_date <= $now_date)
            {
                $next_date = date('Y-m-d', strtotime($max_date) + 86400);
                $insert_re = $this->getRecord($max_date, $next_date);
                if($insert_re['code'] < 0)
                    return $insert_re;
                $insert_data = $insert_re['data'];
                $insert_data['datestamp'] = $max_date;
                // var_dump($insert_data); exit;
                $insert_re = insertByNoModel('t_secret_daily', '', $this->stat_config, $insert_data);
                if($insert_re === false)
                    return array('code'=>-3, 'message'=>"执行insert操作失败，datestamp为：" . $max_date);
                $max_date = $next_date;
            }
        }
        return array('code'=>1, 'message'=>'执行成功');
    }

    private function getRecord($max_date, $next_date)
    {
        $insert_data = array();
        ## 新增帖子 新增帖子参与用户数
        $sql1 = <<<EOF
        SELECT COUNT(*) new_secret, COUNT(DISTINCT user_id) new_secret_user 
        FROM t_secret WHERE `status` = 1 AND create_time >= '{$max_date}' AND create_time < '{$next_date}';
EOF;
        $re1 = $this->query($sql1);
        if($re1 === false)
            return array('code'=>-4, 'message'=>"执行sql失败：" . $sql1);
        $insert_data['new_secret'] = $re1[0]['new_secret'];
        $insert_data['new_secret_user'] = $re1[0]['new_secret_user'];

        ## 新增评论数 新增评论参与的用户数 新增评论覆盖的帖子数
        $sql2 = <<<EOF
        SELECT COUNT(b.comment_id) new_comment, COUNT(DISTINCT b.user_id) new_comment_user, COUNT(a.id) comment_secret
        FROM (SELECT * FROM t_secret WHERE create_time >= '{$max_date}' AND create_time < '{$next_date}' AND `status` = 1) a 
        INNER JOIN (SELECT * FROM t_info_comment 
        WHERE `type` = 2 AND `status` = 1 AND `time` >= '{$max_date}' AND `time` < '{$next_date}') b 
        ON a.id = b.info_id ;
EOF;
        $re2 = $this->query($sql2);
        if($re2 === false)
            return array('code'=>-5, 'message'=>"执行sql失败：" . $sql2);
        $insert_data['new_comment'] = $re2[0]['new_comment'];
        $insert_data['new_comment_user'] = $re2[0]['new_comment_user'];
        $insert_data['comment_secret'] = $re2[0]['comment_secret'];

        ## 新增点赞数 新增点赞参与的用户数 新增点赞覆盖的帖子数
        $sql3 = <<<EOF
        SELECT COUNT(c.id) new_up, COUNT(DISTINCT c.user_id) new_up_user, COUNT(a.id) up_secret FROM 
        (SELECT * FROM t_secret WHERE create_time >= '{$max_date}' AND create_time < '{$next_date}' AND `status` = 1 ) a INNER JOIN 
        (SELECT * FROM t_info_comment WHERE `type` = 2 AND `status` = 1 AND `time` >= '{$max_date}' AND `time` < '{$next_date}') b ON b.info_id = a.id INNER JOIN
        (SELECT * FROM t_commentup_flow WHERE `time` >= '{$max_date}' AND `time` < '{$next_date}' AND `type` = 2) c ON c.comment_id = b.comment_id;
EOF;
        $re3 = $this->query($sql3);
        if($re3 === false)
            return array('code'=>-6, 'message'=>"执行sql失败：" . $sql3);
        $insert_data['new_up'] = $re3[0]['new_up'];
        $insert_data['new_up_user'] = $re3[0]['new_up_user'];
        $insert_data['up_secret'] = $re3[0]['up_secret'];

        return array('code'=>1, 'message'=>'', 'data'=>$insert_data);
    }

    
    private function updateTable($table, $condition, $data)
    {
        $obj_mod = M($table, '', $this->stat_config);
        $result = $obj_mod->where($condition)->save($data);
        return $result;
    }

}
