<?php
namespace Visual\Model;
use Think\Model;

class DistriModel extends Model {
    protected $connection = 'DB_IMED';
    protected $trueTableName = 't_user_info';

    public function _initialize()
    {
        $this->file_name = "inner_user.txt";
        $this->path = dirname(__FILE__) . '/' . $this->file_name;
    }

    ## 获取内部用户列表
    private function getInnerUser()
    {
        $list = '';
        try{
            $f_obj = fopen($this->path, 'r');
            if($f_obj)
            {
                $list = '(';
                while(!feof($f_obj))
                {
                    $item = trim(fgets($f_obj));
                    $list .= "'" . $item . "', ";
                }
                ## 去掉最后一个逗号  换成')'
                $list =  substr($list, 0, count($list) - 3);
                $list .= ')';
            }
            else
                return false;
            fclose($f_obj);
        }
        catch(Exception $e){
            print $e->getMessage();
            // exit();
            return false;
        }
        return $list;
    }

    public function userDetail($type)
    {
        $user_list = $this->getInnerUser();   
        if(empty($user_list))
            return false;
        $field_name = '';
        $field_cnt = '';
        if($type == 1)
        {
            $field_name = 'c.province ';
            $field_cnt = 'c.province ';
        }
        elseif($type == 2)
        {
            $field_name = "(CASE c.hospital_level WHEN 1 THEN '三级特等' WHEN 2 THEN '三级甲等' WHEN 3 THEN '三级乙等' WHEN 4 THEN '三级丙等' WHEN 5 THEN '三级医院'
WHEN 6 THEN '二级甲等' WHEN 7 THEN '二级乙等' WHEN 8 THEN '二级丙等' WHEN 9 THEN '二级医院' WHEN 10 THEN '一级甲等' WHEN 11 THEN '一级乙等'
WHEN 12 THEN '一级丙等' WHEN 13 THEN '一级医院' WHEN 14 THEN '对外专科' WHEN 15 THEN '对外综合' WHEN 16 THEN '其他' END) ";
            $field_cnt = 'c.province ';
        }
        elseif($type == 3)
        {
            $field_name = 'd.category_one ';
            $field_cnt = 'd.category_one ';
        }
        elseif($type == 4)
        {
            $field_name = 'e.title_name ';
            $field_cnt = 'e.title_name ';
        }
        else
            return false;

        $query_sql = <<<EOF
        SELECT {$field_name} field_name, COUNT({$field_cnt}) num
        FROM t_user_info a
        LEFT JOIN t_personal_info b ON a.id = b.user_id
        LEFT JOIN t_hospital_info c ON b.user_hospital = c.id
        LEFT JOIN t_depart_info d ON b.user_depart = d.id
        LEFT JOIN t_title_info e ON b.user_title = e.id
        WHERE a.`status` = 1 AND `password` != '' AND LENGTH(user_uid) = 11
        AND a.user_uid NOT IN {$user_list}
        GROUP BY {$field_cnt}
        ORDER BY COUNT({$field_cnt}) DESC;
EOF;
        // var_dump($query_sql);
        $this->execute("SET NAMES utf8");
        $result = $this->query($query_sql);
        return $result;
    }

}
