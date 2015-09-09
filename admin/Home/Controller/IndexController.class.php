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
}