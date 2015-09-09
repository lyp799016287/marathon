<?php
namespace Manage\Controller;
use Think\Controller;
class ToolController extends Controller {

	private $ControllerList = array();

	public function _initialize(){
		
		$this->tool = D('Tool');
	}

	//获取文件并获取所有路径方法
	private function getfiles($path){  
		if(!is_dir($path)) return;  
		$handle  = opendir($path);  
		$tag  ='public function ';
		while( false !== ($file = readdir($handle)))  
		{  
		    if($file != '.'  && $file!='..')  
		    {  
		        $path2= $path.'/'.$file;  
		        if(is_dir($path2))  
		        {  
		           $this->getfiles($path2);  
		        }else 
		        {  
 
		        	$tmppath = str_replace(APP_PATH, '', $path2);
		        	if(strtolower(trim(substr(strrchr($tmppath, '.'), 1)))=='php'&&strpos($tmppath,'/Runtime/')!==0){
		        		if(strpos($tmppath,'/Controller/')!=0){
		        			$tmppath = str_replace('/Controller/', '/', $tmppath);
		        			$tmppath = str_replace('Controller.class.php', '', $tmppath);
		        			$reader = fopen($path2, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
    
		        			$module = $tmppath;
		        			//echo $module.'<br />';
						    while(!feof($reader)){
						        $line =  fgets($reader, 1024);
						        if(strpos($line, $tag)!=0){
						        	
						        	$linesub= substr($line, strpos($line, $tag)+strlen($tag));
						        	$line = substr($linesub,0,strpos($linesub, '('));
						        	$controller = $line;
						        	if($controller!='_initialize'){
						        		$line = strtolower($module.'/'.$line);
						        	}else{
						        		continue;
						        	}
						        	
						        	array_push($this->ControllerList, $line);

						        }
						    }
						    fclose($reader);

		        		}
		        		
		        	}
		           
		        }  
		    }  
		}  
	}
	/*
	获取所有模块URL
	*/
	public function init(){
		
		$path = APP_PATH;
		$host = $_SERVER['HTTP_HOST'];
		$this->getfiles($path);
		/*foreach ($this->ControllerList as $key => $value) {
			//'http://'.$host.
			echo($value);
			echo '<br />';
		}*/
		$ret = $this->tool->mergeInfo($this->ControllerList);
		$this->ajaxReturn($ret);
		//print_r($this->ControllerList);
		exit();
	}
}