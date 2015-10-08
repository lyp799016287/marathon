<?php
namespace Stat\Model;
use Think\Model;

class DescModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_login_flow';

    public function _initialize()
    {
        $this->stat_config = C('DB_STAT');
        // var_dump($stat_config);
    }

    public function calDevice()
    {
        $sql = "SELECT MAX(login_time) login_time FROM t_user_device_flow";
        $re = queryByNoModel('t_user_device_flow', '', $this->stat_config, $sql);
        if($re === false)
            return array('code'=>-1, 'message'=>"查询错误：" . $sql);
        $where_clause = ' 1';
        if(!is_null($re[0]['login_time']))
        {
            $max_time = $re[0]['login_time'];
            $where_clause = " create_time >'" . $max_time . "'";
        }
        $select_sql = <<<EOF
        SELECT user_uid, device_token, from_channel, create_time FROM imed.`t_login_flow` 
        WHERE `status` IN (1, 2) AND {$where_clause}
EOF;
        // var_dump($select_sql);exit;
        $flow = $this->query($select_sql);
        // var_dump($flow); exit;
        if($flow === false)
            return array('code'=>-2, 'message'=>"查询错误：" . $select_sql);
        for($i = 0; $i < count($flow); $i++)
        {
            // var_dump("into for");
            $insert_data = array();
            $insert_data['user_uid'] = $flow[$i]['user_uid'];
            $insert_data['login_time'] = $flow[$i]['create_time'];
            $insert_data['login_date'] = date('Y-m-d', strtotime($flow[$i]['create_time']));
            $insert_data['channel'] = $flow[$i]['from_channel'];
            $device_ary = explode('_', $flow[$i]['device_token']);
            if(count($device_ary) != 4)
                continue;
            for($k = 0; $k < count($device_ary); $k++)
            {
                switch($k)
                {
                    case 0:
                        $insert_data['user_mobile'] = $device_ary[$k];
                        break;
                    case 1:
                        $insert_data['sdk'] = $device_ary[$k];
                        break;
                    case 2:
                        $insert_data['sys_version'] = $device_ary[$k];
                        break;
                    case 3:
                        $insert_data['app_version'] = $device_ary[$k];
                        break;
                    default:
                        break;
                }
            }
            // var_dump($insert_data); exit;
            // $result = $this->insertOrUpdate($insert_data);
            $result = insertByNoModel('t_user_device_flow', '', $this->stat_config, $insert_data); //有效的登录流水信息全部计入
            if($result === false)
                return array('code'=>-3, 'message'=>"更新数据表错误：" . 't_user_device_flow');
        }
        return array('code'=>1, 'message'=>'执行成功');
    }

    // user_uid  login_time  user_mobile  sdk  sys_version  app_version
    // private function insertOrUpdate($insert_data)
    // {
    //     $uid = $insert_data['user_uid'];
    //     $sql = "SELECT * FROM t_user_device_flow WHERE user_uid = '" . $uid . "'";
    //     $re = queryByNoModel('t_user_device_flow', '', $this->stat_config, $sql);
    //     if($re === false)
    //         return false;
    //     elseif(count($re) > 0) ## update
    //     {
    //         $re = $this->updateData('t_user_device_flow', $insert_data);
    //         return $re;
    //     }
    //     else ## insert
    //     {
    //         $re = insertByNoModel('t_user_device_flow', '', $this->stat_config, $insert_data);
    //         return $re;
    //     }
    // }

    // private function updateData($table, $insert_data)
    // {
    //     $condition['user_uid'] = $insert_data['user_uid'];
    //     $data['login_time'] = $insert_data['login_time'];
    //     $data['user_mobile'] = $insert_data['user_mobile'];
    //     $data['sdk'] = $insert_data['sdk'];
    //     $data['sys_version'] = $insert_data['sys_version'];
    //     $data['app_version'] = $insert_data['app_version'];
    //     $data['channel'] = $insert_data['channel'];
    //     $obj_mod = M($table, '', $this->stat_config);
    //     $result = $obj_mod->where($condition)->setField($data);
    //     return $result;
    // }

//     public function getNewUserInfo()
//     {
//         $sql = "SELECT MAX(datestamp) datestamp FROM t_user_new_flow";
//         $re = queryByNoModel('t_user_new_flow', '', $this->stat_config, $sql);
//         if($re === false)
//             return array('code'=>-1, 'message'=>'数据查询失败');
//         $min_date = '';
//         if(!$re[0]['datestamp'])
//         {
//             $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) datestamp FROM t_user_info WHERE `status` = 1";
//             $re_date = $this->query($sql);
//             if($re_date === false)
//                 return false;
//             $min_date = $re_date[0]['datestamp'];
//         }
//         else
//             $min_date = date("Y-m-d", strtotime("+1 day", strtotime($re[0]['datestamp'])));
//         // var_dump($min_date); exit;
//         $max_date = date("Y-m-d", time());
//         while($min_date < $max_date)
//         {
//             $bgn = $min_date;
//             $end = date('Y-m-d', strtotime("+1 day", strtotime($min_date)));
//             $sql = "SELECT DISTINCT user_uid FROM t_user_info WHERE `status` = 1 AND create_time >= '" . $bgn . "' AND create_time < '" . $end . "'";
//             $re_list = $this->query($sql);
//             if($re_list === false)
//                 return false;
//             if(count($re_list) > 0)
//             {
//                 // 批量插入表t_user_new_flow
//                 $obj_mod = M('t_user_new_flow', '', $this->stat_config);
//                 $obj_mod->execute("SET NAMES utf8");
//                 for($i = 0; $i < count($re_list); $i++)
//                 {
//                     $re_list[$i]['datestamp'] = $min_date;
//                     $obj_mod->add($re_list[$i]);
//                 }
//                 // exit;
//                 ## 更新当天用户的相关信息
//                 $this->updateNewInfo($min_date);
//                 $min_date = $end;
//             }  
//         }
//     }


//     private function updateNewInfo($date)
//     {
//         $sql = <<<EOF
//         SELECT a.user_uid, b.user_mobile, b.sdk, b.sys_version , b.app_version, b.channel 
//         FROM (SELECT * FROM t_user_new_flow WHERE datestamp = '{$date}') a 
//         LEFT JOIN (SELECT * FROM stat.`t_user_device_flow` WHERE login_date = '{$date}') b 
//         ON a.user_uid = b.user_uid 
// EOF;
//         // var_dump($sql); exit;
//         $re = queryByNoModel('t_user_new_flow', '', $this->stat_config, $sql);
//         if($re === false)
//             return false;
//         for($i = 0; $i < count($re); $i++)
//         {
//             $data = $condition = array();
//             $condition['datestamp'] = $date;
//             $condition['user_uid'] = $re[$i]['user_uid'];
            
//             $data['user_mobile'] = $re[$i]['user_mobile'];
//             $data['sdk'] = $re[$i]['sdk'];
//             $data['sys_version'] = $re[$i]['sys_version'];
//             $data['app_version'] = $re[$i]['app_version'];
//             $data['channel'] = $re[$i]['channel'];
//             $obj_mod = M('t_user_new_flow', '', $this->stat_config);
//             $re = $obj_mod->where($condition)->setField($data);
//             if($re === false)
//                 return false;
//         }
//         return true;
//     }

    ## 每天各个渠道的新用户  活跃用户  APP被启动的次数
    public function userChannel()
    {
        $min_date = '';
        $sql = "SELECT MAX(datestamp) datestamp FROM t_user_channel";
        $re = queryByNoModel('t_user_channel', '', $this->stat_config, $sql);
        if($re === false)
            return array('code'=>-1, 'message'=>'数据查询失败');
        elseif($re[0]['datestamp'])
        {
            $min_date = date('Y-m-d', strtotime("+1 day", strtotime($re[0]['datestamp'])));
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) datestamp FROM t_user_info WHERE `status` = 1";
            $re = $this->query($sql);
            if($re === false)
                return array('code'=>-2, 'message'=>'数据查询失败');
            $min_date = $re[0]['datestamp'];
        }
        $max_date = date('Y-m-d', time());
        // var_dump($min_date); exit;
        ## 获取每天各个渠道的新用户
        $type = 1; ## 表明查询的是渠道相关的信息
        while($min_date < $max_date)
        {
            $new_user = $this->newUserChannel($min_date, $type);
            $all_user = $this->allUserChannel($min_date, $type);
            $open_times = $this->openChannel($min_date, $type);
            // var_dump($new_user);
            // var_dump($all_user);
            // var_dump($open_times);
            if($new_user === false || $all_user === false || $open_times === false)
                return array('code'=>-3, 'message'=>'数据查询失败');
            
            for($i = 0; $i < count($all_user); $i++)
            {
                $insert_data = array('datestamp'=>'', 'channel'=>'', 'new_user'=>0, 'active_user'=>0, 'open_times'=>0);
                $insert_data['datestamp'] = $min_date;
                $channel = $insert_data['channel'] = $all_user[$i]['channel'];

                for($j = 0; $j < count($open_times); $j++)
                {
                    if($open_times[$j]['channel'] == $channel)
                    {
                        $insert_data['open_times'] = is_null($open_times[$j]['cnt']) ? 0 : $open_times[$j]['cnt'];
                        break;
                    }
                        
                }
                for($k = 0; $k < count($new_user); $k++)
                {
                    if($new_user[$k]['channel'] == $channel)
                    {
                        $insert_data['new_user'] = is_null($new_user[$k]['cnt']) ? 0 : $new_user[$k]['cnt'];
                        break;
                    }
                }
                $insert_data['active_user'] = ($all_user[$i]['cnt'] - $insert_data['new_user'] >= 0) ? ($all_user[$i]['cnt'] - $insert_data['new_user']) : 0;
                // var_dump($insert_data); exit;
                ## 插入表
                $obj_mod = M('t_user_channel', '', $this->stat_config);
                $obj_mod->execute("SET NAMES utf8");
                $result = $obj_mod->add($insert_data);
                if($result === false)
                    return array('code'=>-4, 'message'=>'数据插入失败');
            } // end of FOR
            $min_date = date('Y-m-d', strtotime('+1 day', strtotime($min_date)));
        } // end of WHILE
        return array('code'=>1, 'message'=>'执行成功');
    }

    private function newUserChannel($date, $type)
    {
        if($type == 1)
            $field = 'channel';
        elseif($type == 2)
            $field = 'app_version';
        elseif($type == 3)
            $field = 'sdk';
        else
            return false;
        $date_end = date('Y-m-d', strtotime("+1 day", strtotime($date)));
        $tmp_sql = 'SET NAMES utf8';
        $this->execute($tmp_sql);
        $sql = <<<EOF
            SELECT (CASE WHEN b.{$field} IS NULL THEN '其他' ELSE b.{$field} END) {$field}, COUNT(DISTINCT a.user_uid) cnt FROM 
            (SELECT * FROM t_user_info WHERE `status` = 1 AND create_time >= '{$date}' AND create_time < '{$date_end}') a 
            LEFT JOIN stat.`t_user_device_flow` b ON a.user_uid= b.user_uid 
            GROUP BY b.{$field} 
            ORDER BY b.{$field}
EOF;
        return $this->query($sql);
    }


    private function allUserChannel($date, $type)
    {
        if($type == 1)
            $field = 'channel';
        elseif($type == 2)
            $field = 'app_version';
        elseif($type == 3)
            $field = 'sdk';
        else
            return false;
        $date_end = date('Y-m-d', strtotime("+1 day", strtotime($date)));
        $tmp_sql = 'SET NAMES utf8';
        $this->execute($tmp_sql);
        $sql = <<<EOF
            SELECT (CASE WHEN b.{$field} IS NULL THEN '其他' ELSE b.{$field} END) {$field}, COUNT(DISTINCT a.user_uid) cnt FROM 
            (SELECT * FROM t_login_flow WHERE `status` IN(1, 2) AND create_time >= '{$date}' AND create_time < '{$date_end}') a 
            LEFT JOIN stat.`t_user_device_flow` b ON a.user_uid= b.user_uid 
            GROUP BY b.{$field}
            ORDER BY b.{$field}
EOF;
        return $this->query($sql);
    }
    
    private function openChannel($date, $type)
    {
        if($type == 1)
            $field = 'channel';
        elseif($type == 2)
            $field = 'app_version';
        elseif($type == 3)
            $field = 'sdk';
        else
            return false;
        $date_end = date('Y-m-d', strtotime("+1 day", strtotime($date)));
        $tmp_sql = 'SET NAMES utf8';
        $this->execute($tmp_sql);
        $sql = <<<EOF
            SELECT (CASE WHEN b.{$field} IS NULL THEN '其他' ELSE b.{$field} END) {$field}, COUNT(a.user_uid) cnt FROM 
            (SELECT * FROM t_login_flow WHERE `status` IN(1, 2) AND create_time >= '{$date}' AND create_time < '{$date_end}') a 
            LEFT JOIN stat.`t_user_device_flow` b ON a.user_uid= b.user_uid 
            GROUP BY b.{$field} 
            ORDER BY b.{$field}
EOF;
        return $this->query($sql);
    }


    public function userVersion()
    {
        $min_date = '';
        $sql = "SELECT MAX(datestamp) datestamp FROM t_user_app_version";
        $re = queryByNoModel('t_user_app_version', '', $this->stat_config, $sql);
        if($re === false)
            return array('code'=>-1, 'message'=>'数据查询失败');
        elseif($re[0]['datestamp'])
        {
            $min_date = date('Y-m-d', strtotime("+1 day", strtotime($re[0]['datestamp'])));
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) datestamp FROM t_user_info WHERE `status` = 1";
            $re = $this->query($sql);
            if($re === false)
                return array('code'=>-2, 'message'=>'数据查询失败');
            $min_date = $re[0]['datestamp'];
        }
        $max_date = date('Y-m-d', time());
        // var_dump($min_date);
        // var_dump($max_date); exit;
        $type = 2; ## 表明查询的是APP版本相关的信息
        while($min_date < $max_date)
        {
            // var_dump($min_date);
            // $new_user = $this->newUserChannel($min_date, $type);
            // 该版本的新增用户 包含当天注册新增+升级到该版本的用户
            $new_user = $this->versionNewUser($min_date);
            // var_dump($new_user); exit;
            $update_user = $this->versionUpdateUser($min_date); ## 升级用户
            $all_user = $this->allUserChannel($min_date, $type);
            $open_times = $this->openChannel($min_date, $type);

            if($new_user === false || $all_user === false || $open_times === false || $update_user === false)
                return array('code'=>-3, 'message'=>'数据查询失败');
            
            for($i = 0; $i < count($all_user); $i++)
            {
                $insert_data = array('datestamp'=>'', 'app_version'=>'', 'new_user'=>0, 'update_user'=>0, 'active_user'=>0, 'open_times'=>0);
                $insert_data['datestamp'] = $min_date;
                $version = $insert_data['app_version'] = $all_user[$i]['app_version'];

                for($j = 0; $j < count($open_times); $j++)
                    if($open_times[$j]['app_version'] == $version)
                    {
                        $insert_data['open_times'] = is_null($open_times[$j]['cnt']) ? 0 : $open_times[$j]['cnt'];
                        break;
                    }

                for($k = 0; $k < count($new_user); $k++)
                    if($new_user[$k]['app_version'] == $version)
                    {
                        $insert_data['new_user'] = is_null($new_user[$k]['cnt']) ? 0 : $new_user[$k]['cnt'];
                        break;
                    }

                for($n = 0; $n < count($update_user); $n++)
                    if($update_user[$n]['app_version'] == $version)
                    {
                        $insert_data['update_user'] = is_null($update_user[$n]['cnt']) ? 0 : $update_user[$n]['cnt'];
                        break;
                    }

                $insert_data['active_user'] = ($all_user[$i]['cnt'] - $insert_data['new_user'] >= 0) ? ($all_user[$i]['cnt'] - $insert_data['new_user']) : 0;
                ## 插入表
                $obj_mod = M('t_user_app_version', '', $this->stat_config);
                $obj_mod->execute("SET NAMES utf8");
                $result = $obj_mod->add($insert_data);
                if($result === false)
                    return array('code'=>-4, 'message'=>'数据插入失败');
            } // end of FOR
            $min_date = date('Y-m-d', strtotime('+1 day', strtotime($min_date)));
        } // end of WHILE

        return array('code'=>1, 'message'=>'执行成功');
    }


    private function versionNewUser($date)
    {
        $sql_new = <<<EOF
            SELECT b.app_version, COUNT(b.user_uid) cnt FROM 
            (SELECT * FROM t_user_device_flow WHERE login_date = '{$date}') b
            LEFT JOIN 
            (SELECT user_uid, MAX(app_version) max_version_before
            FROM t_user_device_flow 
            WHERE user_uid IN (SELECT user_uid FROM t_user_device_flow WHERE login_date = '{$date}') AND login_date < '{$date}'
            GROUP BY user_uid ) a
            ON a.user_uid = b.user_uid 
            WHERE a.user_uid IS NULL OR (max_version_before < app_version)
            GROUP BY b.app_version 
EOF;
        // var_dump($sql_new);
        return queryByNoModel('t_user_device_flow', '', $this->stat_config, $sql_new);
    }


    private function versionUpdateUser($date)
    {
        $sql_update = <<<EOF
            SELECT app_version, COUNT(a.user_uid) cnt FROM 
            (SELECT user_uid, MAX(app_version) max_version_before
            FROM t_user_device_flow 
            WHERE user_uid IN (SELECT user_uid FROM t_user_device_flow WHERE login_date = '{$date}') AND login_date < '{$date}'
            GROUP BY user_uid) a
            LEFT JOIN 
            (SELECT * FROM t_user_device_flow WHERE login_date = '{$date}') b
            ON a.user_uid = b.user_uid 
            WHERE max_version_before < app_version
            GROUP BY app_version 
EOF;
        return queryByNoModel('t_user_device_flow', '', $this->stat_config, $sql_update);
    }

    public function userSysVersion()
    {
        $min_date = '';
        $sql = "SELECT MAX(datestamp) datestamp FROM t_user_sys_version";
        $re = queryByNoModel('t_user_sys_version', '', $this->stat_config, $sql);
        if($re === false)
            return array('code'=>-1, 'message'=>'数据查询失败');
        elseif($re[0]['datestamp'])
        {
            $min_date = date('Y-m-d', strtotime("+1 day", strtotime($re[0]['datestamp'])));
        }
        else
        {
            $sql = "SELECT MIN(SUBSTRING(CAST(create_time AS CHAR(20)), 1, 10)) datestamp FROM t_user_info WHERE `status` = 1";
            $re = $this->query($sql);
            if($re === false)
                return array('code'=>-2, 'message'=>'数据查询失败');
            $min_date = $re[0]['datestamp'];
        }
        $max_date = date('Y-m-d', time());

        $type = 3; ## 表明查询的是手机版本相关的信息
        while($min_date < $max_date)
        {
            // var_dump($min_date);
            $new_user = $this->newUserChannel($min_date, $type);
            $all_user = $this->allUserChannel($min_date, $type);
            $open_times = $this->openChannel($min_date, $type);
            // var_dump($new_user);
            // var_dump($all_user);
            // var_dump($open_times);
            if($new_user === false || $all_user === false || $open_times === false)
                return array('code'=>-3, 'message'=>'数据查询失败');
                         
            for($i = 0; $i < count($all_user); $i++)
            {
                $insert_data = array('datestamp'=>'', 'sys_version'=>'', 'new_user'=>0, 'active_user'=>0, 'open_times'=>0);
                $insert_data['datestamp'] = $min_date;
                $version = $insert_data['sys_version'] = $all_user[$i]['sdk'];

                for($j = 0; $j < count($open_times); $j++)
                {
                    if($open_times[$j]['sdk'] == $version)
                    {
                        $insert_data['open_times'] = is_null($open_times[$j]['cnt']) ? 0 : $open_times[$j]['cnt'];
                        break;
                    }
                        
                }
                for($k = 0; $k < count($new_user); $k++)
                {
                    if($new_user[$k]['sdk'] == $version)
                    {
                        $insert_data['new_user'] = is_null($new_user[$k]['cnt']) ? 0 : $new_user[$k]['cnt'];
                        break;
                    }
                }
                $insert_data['active_user'] = ($all_user[$i]['cnt'] - $insert_data['new_user'] >= 0) ? ($all_user[$i]['cnt'] - $insert_data['new_user']) : 0;
                // var_dump($insert_data); exit;
                ## 插入表
                $obj_mod = M('t_user_sys_version', '', $this->stat_config);
                $obj_mod->execute("SET NAMES utf8");
                $result = $obj_mod->add($insert_data);
                if($result === false)
                    return array('code'=>-4, 'message'=>'数据插入失败');
                    
            } // end of FOR
            $min_date = date('Y-m-d', strtotime('+1 day', strtotime($min_date)));
        } // end of WHILE

        return array('code'=>1, 'message'=>'执行成功');
    }

}
