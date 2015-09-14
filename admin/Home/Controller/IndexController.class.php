<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

	public function _initialize(){
		//var_dump(C('DB_ADMIN'));exit();
		$this->index = D('Index');
	}

    public function index(){
        $menus = $this->index->getMenu();

        $menuOrder = array();
        foreach ($menus as $k => $v) {
        	if($v['parent_id']==0){
        		$menuOrder[$v['id']]=array('top'=>$v,'sub'=>array());
        	}else{
        		array_push($menuOrder[$v['parent_id']]['sub'],$v);
        	}
        }
        
        $this->assign('menus',$menuOrder);

        $this->display("index");
        //print_r($menuOrder);//exit();
    }

	/*public function download(){
		import('Vendor.StatExcel.StatExcel');
		$excel = new \StatExcel();
		//var_dump($excel);

		$columns = array(
			array('id' => 's_date', 'name' => '时间', 'isSort' => true, 'issortshow' => true, 'width' => 90),
			array('id' => 'module_name', 'name' => '页面模块', 'width' => 120, 'isSort' => true, 'issortshow' => true),
			array('id' => 'click_num', 'name' => '点击量', 'width' => 120, 'isSort' => true, 'issortshow' => true, 'type' => 'int', 'align' => 'right'),
			array('id' => 'order_num', 'name' => '下单笔数', 'width' => 120, 'isSort' => true, 'issortshow' => true, 'type' => 'int', 'align' => 'right'),
			array('id' => 'order_fee', 'name' => '下单金额', 'width' => 120, 'isSort' => true, 'issortshow' => true, 'type' => 'int', 'align' => 'right'),
			array('id' => 'trans_rate', 'name' => '转化率', 'width' => 120, 'isSort' => true, 'issortshow' => true, 'align' => 'right')
		);

		$report_title = "无线实时统计报表";

		$excel->addParams(
			array(
				'title' => $report_title,
				'header' => array($columns),
				'data' => array()	//数据集
			)
		);		
		$excel->exportAsXsl($report_title);
	}*/
}