<?php
namespace Stat\Model;
use Think\Model;

class InfopatchModel extends Model {
    protected $connection = 'DB_STAT';
    protected $trueTableName = 't_info_daily';

    public function _initialize()
    {
        $this->imed_config = C('DB_IMED');
        // var_dump($stat_config);
    }

    ## 补充t_info_daily表中的title  pub_time
    public function infoPatch()
    {
        $selectSql = "SELECT DISTINCT info_id FROM t_info_daily WHERE pub_time IS NULL OR pub_time = ''";
        $result = $this->query($selectSql);
        if($result === false)
            return array('code'=>-1, 'message'=>"查询错误");
        if(count($result) == 0)
            return array('code'=>1, 'message'=>"执行成功");
        $in_str = "(";
        for($i = 0; $i < count($result); $i++)
            if($i != count($result) - 1)
                $in_str .= $result[$i]['info_id'] . ", ";
            else
                $in_str .= $result[$i]['info_id'] . ") ";
        var_dump($in_str);
        $selectSql = "SELECT info_id, title, create_time FROM t_info_summary WHERE info_id IN " . $in_str;
        $re = queryByNoModel('t_info_summary', '', $this->imed_config, $selectSql);
        if($re === false)
            return array('code'=>-2, 'message'=>'查询错误');
        $error_str = '';
        for($j = 0; $j < count($re); $j++)
        {
            $update = $this->updateInfo($re[$j]);
            if($update === false)
                $error_str .= $re[$j]['info_id'] . ', ';
        }
        if($error_str != '')
            return array('code'=>-3, 'message'=>'更新错误：' . $error_str);
        else
            return array('code'=>1, 'message'=>'执行成功');
    }

    private function updateInfo($info)
    {
        $title = $info['title'];
        $pub_time = $info['create_time'];
        $info_id = $info['info_id'];
        $updateSql = <<<EOF
        UPDATE t_info_daily
        SET title = '{$title}',
            pub_time = '{$pub_time}'
        WHERE info_id = {$info_id}
EOF;
        $tmp_sql = "SET NAMES utf8";
        $this->execute($tmp_sql);
        return $this->execute($updateSql);
    }
}
