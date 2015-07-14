<?php
namespace Edit\Model;
use Think\Model;

class ImedModel extends Model {
        protected $connection = 'DB_IMED';
        protected $trueTableName = 't_material';

        public function pubInfo($title, $source, $pub_date, $url, $summary, $author, $content, $imgTag)
        {
            $materialAry = array('url'=>$imgTag, 'status'=>1);
            $re = $this->add($materialAry);
            if(!$re)
                return false;
            $materialId = intval($re);

            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);
            $summarySql = <<<EOF
            INSERT INTO t_info_summary(type, title, author, pub_date, source, src_type, src_url, summary, material_id, `status`)
            VALUES(2, '{$title}', '{$author}', '{$pub_date}', '{$source}', 0, '{$url}', '{$summary}', {$materialId}, 1)
EOF;
            $insertRe = $this->execute($summarySql);
            if(!$insertRe)
                return false;
            ## 获得刚插入这一条的info_id
            $selectSql = "SELECT info_id FROM t_info_summary WHERE `type` = 2 AND material_id = " . $materialId;
            $idRe = $this->query($selectSql);

            if(!$idRe)
                return false;
            $infoId = $idRe[0]['info_id'];

            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);
            $entitySql = "INSERT INTO t_info_entity(info_id, content) VALUES(" . $infoId . ", '" . $content . "')";

            $entityRe = $this->execute($entitySql);
            if(!$entityRe)
                return false;

            return true;

        }

}
