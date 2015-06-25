<?php
namespace Edit\Model;
use Think\Model;

class ListModel extends Model {
        protected $connection = 'DB_ADMIN';
        protected $trueTableName = 't_info_raw';

        public function getSrc()
        {
            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);
            $selectSql = "SELECT distinct source FROM t_info_raw;";
            $res = $this->query($selectSql);
            return $res;
        }

        public function getNewsList($page,$pagesize=10)
        {
            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);

            $bgn_page = $pagesize * $page;
        	$selectSql = "SELECT id, title, `status`,pub_date FROM t_info_raw ORDER BY `status` LIMIT " . $bgn_page . ", ".$pagesize;
        	//var_dump($selectSql);exit;
        	$res = $this->query($selectSql);
            return $res;
        }

        public function getNewsListCount($where='')
        {
            $selectSql = "SELECT count(1) as total FROM t_info_raw where 1=1 ".$where;
            $counter = 0;
            //var_dump($selectSql);exit;
            $res = $this->query($selectSql);
            
            if($res!==false){
                $counter=$res[0]['total'];
            }
            return $counter;
        }

        public function searchResult($keyword, $source, $bgn_date, $end_date, $status,$page,$pagesize=20)
        {
            $tmpSql = "SET NAMES 'utf8';";
            $this->execute($tmpSql);

            $selectSql = "SELECT id, title, `status`,pub_date FROM t_info_raw WHERE 1=1 ";
            $clause = array();
            $clause['key'] = ($keyword != '') ? (" and title LIKE '%" . $keyword . "%' ") : '';
            $clause['source'] = ($source != '') ? (" and source = '" . $source . "' ") : '';
            
            $clause['date'] = '';
            if($bgn_date != ''){
                $clause['date'] .=' and '." pub_date >= '" . $bgn_date."'" ;
            }
            if($end_date != ''){
                $clause['date'] .=' and '." pub_date <=  '" . $end_date."'" ;
            }
            

            $clause['status'] = ($status != '') ? ("and  status = " . $status) : '';
            $andFlag = 0;
            /*
            foreach($clause as $key=>$val)
            {
                if($andFlag == 0)
                {
                    $selectSql .= $val;
                    if($val != '')
                        $andFlag++;
                }
                else
                    if($val != '')
                    {
                        $selectSql .= " AND " . $val;
                        $andFlag++;
                    }
            }
            */
            foreach($clause as $key=>$val)
            {
                $selectSql .= $val;
            }

            //var_dump($selectSql);exit();
            $bgn_page = $pagesize * $page;
            $selectSql.=" LIMIT " . $bgn_page . ", ".$pagesize;
            $re = $this->query($selectSql);
            return $re;
        }

        public function searchResultCount($keyword, $source, $bgn_date, $end_date, $status)
        {
            $selectSql = "SELECT count(1) as total FROM t_info_raw WHERE 1=1 ";
            $clause = array();
            $clause['key'] = ($keyword != '') ? (" and title LIKE '%" . $keyword . "%' ") : '';
            $clause['source'] = ($source != '') ? (" and source = '" . $source . "' ") : '';
            
            $clause['date'] = '';
            if($bgn_date != ''){
                $clause['date'] .=' and '." pub_date >= '" . $bgn_date."'" ;
            }
            if($end_date != ''){
                $clause['date'] .=' and '." pub_date <=  '" . $end_date."'" ;
            }
            

            $clause['status'] = ($status != '') ? ("and  status = " . $status) : '';
            $andFlag = 0;
            /*
            foreach($clause as $key=>$val)
            {
                if($andFlag == 0)
                {
                    $selectSql .= $val;
                    if($val != '')
                        $andFlag++;
                }
                else
                    if($val != '')
                    {
                        $selectSql .= " AND " . $val;
                        $andFlag++;
                    }
            }
            */
            foreach($clause as $key=>$val)
            {
                $selectSql .= $val;
            }
            //var_dump($selectSql);exit();
            $counter = 0;
            //var_dump($selectSql);exit;
            $res = $this->query($selectSql);
            if($res!==false){
               $counter=$res[0]['total'];
            }
            return $counter;
        }
    
}
