<?php
namespace Edit\Model;
use Think\Model;

class DetailModel extends Model {
        protected $connection = 'DB_ADMIN';
        protected $trueTableName = 't_info_raw';

        public function detailInfo($id)
        {
            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);
            $selectSql = "SELECT * FROM t_info_raw WHERE id = " . $id . ";";
            $res = $this->query($selectSql);
            return $res;
        }

        public function updateInfo($id, $title, $summary, $content, $status)
        {
            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);
            $updateSql = "UPDATE t_info_raw SET title = '" . $title . "', summary = '" . $summary . "', content = '" . $content . "', status = " . $status . " WHERE id = " . $id;
            $re = $this->execute($updateSql);
            return $re;
        }

        public function updateStatus($id, $status)
        {
            $updateSql = "UPDATE t_info_raw SET `status` = " . $status . " WHERE id = " . $id;
            $re = $this->execute($updateSql);
            return $re;
        }

        ## 删除状态的重置
        public function delStatus($id)
        {
            $selectSql = "SELECT `status` FROM t_info_raw WHERE id = " . $id;
            $re = $this->query($selectSql);
            if(!$re)
                return false;
            $status = $re[0]['status'];
            $newStat = 0;
            if($status < 3)
                $newStat = 4;
            else
                $newStat = 5;
            $re = $this->updateStatus($id, $newStat);
            return $re;
        }


}
