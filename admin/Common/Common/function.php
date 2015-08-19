<?php
/**
 * 验证手机号码
 *
 *
 * @param        phone    $phone, 电话号码
 * @param        string   $type, CHN中国大陆电话号码, INT国际电话号码
 *
 * @return        bool    正确返回true, 错误返回false
 */
function checkMobilePhone($phone, $type = 'CHN'){
	
	$ret = false;
	switch ($type) {
		case "CHN":
			$ret = (preg_match("/^((\(\d{3}\))|(\d{3}\-))?1\d{10}$/", trim($phone)) ? true : false);
			break;
		case "INT":
			$ret = (preg_match("/^((\(\d{3}\))|(\d{3}\-))?\d{6,20}$/", trim($phone)) ? true : false);
			break;
	}

	if ($ret === false) {
		return false;
	}

	return true;
}

/**
 * 验证手机验证码
 *
 *
 * @param	code	$code, 验证码六位数字
 *
 * @return	bool	正确返回true, 错误返回false
 */
function checkVcode($code){
	
	$ret = false;

	$ret = (preg_match("/^\d{6}$/", trim($code)) ? true : false);

	if ($ret === false) {
		return false;
	}

	return true;
}


/**
 * 导入脏话库
 * return array()
 */
function dirtyInit(){
	$fileName = THINK_LIB_PATH.'Conf/dirty.txt';
	$lines = file($fileName);

	
	if ( empty($lines) ) {
		return false;
	}

	$line_num = 0;
	$dirtyWords = array();

	foreach ($lines as $line) {
		$line_num++;
		$line = trim($line);

		if ( empty($line) ) {
			continue;
		}

		// 忽略注释行
		if ( $line[0] == '#' ) {
			continue;
		}

		$word = explode('|', $line);
		if ( !is_array($word) || count($word) != 2 ) {
			continue;
		}

		$dirtyWords[$word[0]] = $word[1];
	}
	return $dirtyWords;
}


/**
 * 检查字符串中是否有脏话
 *
 * @param string	str    字符串
 * @return bool		如果字符串中包含脏话的话返回true,否则返回false
 */
function hasDirty($str) {

	$dirtyWords = dirtyInit();

	while ( list($key, $v) = each($dirtyWords) ) {
		if ( strpos($str, (string)$key) !== false) {
			return true;
			break;
		}
	}

	return false;
}

/**
 * 检查一个单词是否是脏话
 *
 * @param string word    单词
 * @return int		如果字符串中包含脏话的话返回脏话等级,否则返回false
 */
function isDirty($word) {

	$dirtyWords = dirtyInit();
	
	if ( isset($dirtyWords[$word]) ) {
		return $dirtyWords[$word];
	}

	return false;
}

/**
 * 把字符串中的脏话替换成指定的字符
 *
 * @param string	str    字符串
 * @param string	rChar    脏话需要替换的字符
 *
 * @return string
 */
function replaceDirty($str, $rChar='*') {
	
	$dirtyWords = dirtyInit();

	while ( list($key, $v) = each($dirtyWords) ) {
		if ( strpos($str, $key) !== false ) {
			$rStr = str_pad('', mb_strlen($key, 'utf8'), $rChar);
			$str  = mbStrReplace($str, $key, $rStr, 0, 'utf8');
		}
	}
	return $str;
}

function mbStrReplace($haystack, $search, $replace, $offset = 0, $encoding = 'auto'){
	$len_sch = mb_strlen($search,$encoding);
	$len_rep = mb_strlen($replace,$encoding);

	while (($offset = mb_strpos($haystack, $search, $offset, $encoding))!== false){
		$haystack = mb_substr($haystack, 0, $offset, $encoding) . $replace . mb_substr($haystack, $offset + $len_sch, 1000, $encoding);
		$offset = $offset + $len_rep;
		if ($offset > mb_strlen($haystack, $encoding))
			break;
	}
	return $haystack;
}

/**
 * 判断字符串是否是汉字
 *
 * @param string	str    字符串
 *
 * @return string
 */
function checkChineseName($str){
	$ret = false;

	$ret = (preg_match("/^[\x{4e00}-\x{9fa5}]{2,6}$/u", trim($str)) ? true : false);

	if ($ret === false) {
		return false;
	}

	return true;
}

/**
 * 生成手机验证码
 *
 *
 * @return 随机的6位数字
 */
function getAuthCode($len = 6){
	$arr_str= array(0,1,2,3,4,5,6,7,8,9);
	$string = '';
	
	for($i = 0; $i< $len; $i++){
		$string .= ''.$arr_str[mt_rand(0,9)];
	}
	return $string;
}



/**
 *发送短信
 * @param	$phone	手机号码
 * @param	$acode	验证码
 * @return	bool
 *
 **/
function postSms($phone, $acode, $try=3){
	//改demo的功能是群发短信和发单条短信。（传一个手机号就是发单条，多个手机号既是群发）
	//您把序列号和密码还有手机号，填上，直接运行就可以了
	//如果您的系统是utf-8,请转成GB2312 后，再提交、
	//请参考 'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,'您好测试短信[XXX公司]'),//短信内容

	$flag = 0; 
	//要post的数据 
	$argv = array( 
		'sn'=>C('IMED_SMS_SN'), ////替换成您自己的序列号
		'pwd'=>strtoupper(md5(C('IMED_SMS_SN').C('IMED_SMS_PWD'))), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
		'mobile'=>$phone,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
		//'content'=>'(1571)医道注册验证码.【医知科技】 ',//短信内容
		'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,'('.$acode.')'.C('LOGIN_SMS_CONTENT')),//短信内容
		'ext'=>'',		
		'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
		'rrid'=>''
	); 
	//构造要post的字符串 
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
			$params .= "&"; 
			$flag = 1; 
		}
		$params.= $key."="; $params.= urlencode($value); 
		$flag = 1; 
	} 

	$length = strlen($params);
	
	
	//创建socket连接 
	$fp = fsockopen("sdk2.entinfo.cn",8060,$errno,$errstr,10) or exit($errstr."--->".$errno); 
	//构造post请求的头 
	$header = "POST /webservice.asmx/mt HTTP/1.1\r\n"; 
	$header .= "Host:sdk2.entinfo.cn\r\n"; 
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
	$header .= "Content-Length: ".$length."\r\n"; 
	$header .= "Connection: Close\r\n\r\n"; 
	//添加post的字符串 
	$header .= $params."\r\n"; 
	//发送post的数据 
	fputs($fp,$header); 
	$inheader = 1; 
	while (!feof($fp)) { 
		$line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
		if ($inheader && ($line == "\n" || $line == "\r\n")) { 
			 $inheader = 0; 
		} 
		if ($inheader == 0) { 
			// echo $line; 
		} 
	} 
	//<string xmlns="http://tempuri.org/">-5</string>
	$line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
	$line=str_replace("</string>","",$line);
	$result=explode("-",$line);

	if(count($result)>1){
		return false;
	}else{
		return true;
	}
}

/**
 *基础方法，无模型文件的数据插入方法
 * @params $name		table_name 
 * @params $prefix		表前缀
 * @params $config		数据库配置
 * @params $data		操作数据
 * @return false/insert_id
 *
 **/
function insertByNoModel($name, $prefix ='', $config, $data){
	if(empty($name)){
		return false;
	}

	if(empty($config)){
		return false;
	}

	if(empty($data)){
		return false;
	}

	$obj_mod = M($name, $prefix, $config);
	$obj_mod->execute("SET NAMES utf8");
	
	return $obj_mod->add($data);
}

/**
 *基础方法，无模型文件的查询方法
 * @params $name		table_name 
 * @params $prefix		表前缀
 * @params $config		数据库配置
 * @params $sql			查询语句
 * @return false/array
 *
 **/
function queryByNoModel($name, $prefix ='', $config, $sql){
	if(empty($name)){
		return false;
	}

	if(empty($config)){
		return false;
	}

	if(empty($sql)){
		return false;
	}

	$obj_mod = M($name, $prefix, $config);
	$obj_mod->execute("SET NAMES utf8");
	
	return $obj_mod->query($sql);
}

/**
 * 时间差计算
 * @params $startTime		开始时间 
 * @params $endTime			结束时间
 * @params $diffType		计差变量 day, hour, minute, second
 * @return int
 *
 **/
function GetDateDiff($startTime, $endTime, $diffType) {
    //将xxxx-xx-xx xx:xx:xx的时间格式，转换为数字
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);

    //将计算间隔类性字符转换为小写
    $diffType = strtolower($diffType);

    //作为除数的数字
    $divNum = 1;
    switch ($diffType) {
        case "second":
            $divNum = 1000;
            break;
        case "minute":
            $divNum =  60;
            break;
        case "hour":
            $divNum =  3600;
            break;
        case "day":
            $divNum = 3600 * 24;
			break;
        default:
            break;
    }
	return floor(($endTime - $startTime) / $divNum);
}

/**
 * 将字节数组转化为String类型的数据
 * @param $bytes 字节数组
 * @return 一个String类型的数据
 *
 **/
function getStr($bytes){
	$str = '';
	foreach($bytes as $ch){
		$str .= chr($ch);
	}
	
	return $str;
}

/**
 * 转换一个String字符串为byte数组
 * @param $string 需要转换的字符串
 * @param $bytes 目标byte数组
 *
 **/
function getBytes($string) { 
	$bytes = array(); 
	for($i = 0; $i < strlen($string); $i++){ 
		 if(ord($string[$i]) >= 128){
			$bytes[] = ord($string[$i]) - 256; 
		 }else{
			$bytes[] = ord($string[$i]); 
		 } 
	} 
	return $bytes; 
}

/**
 * 解密函数
 * @param $pwd 原密码
 * @return string 解密后秘密
 *
 **/
function dcrypt($pwd){
	$key = array(9, 8, 7, 3, 2, 1);
	
	$t_buffer = getBytes($pwd);
	

	$len = count($t_buffer);
	
	$temp = array();

	for($i=0; $i < $len; $i++){
		$temp[$i] = ($t_buffer[$i] ^ $key[$i % 6]);
	}

	return getStr($temp);
}

/**
 * 过滤用户输入字符中的不合法字符
 *
 * 目前主要直接把一些特殊字符替换成空格，并去掉前后空格
 *
 * @param    string    $str    用户输入的字符
 * @return   string    过滤后输出的字符
 */
function filterInput($str){
	if (empty($str)) return '';

	// 需要替换的特殊字符
	$specialStr = array('\\', '\'', '"', '`', '&', '/', '<', '>');
	$str = str_replace($specialStr, '', $str);

	// 超过一定字符集范围的也需要替换成空格
	$str = trim($str);
	$asciiCode = '/[\x00-\x1f\x7f]/is';
	$str = preg_replace($asciiCode, '', $str);

	return $str;
}


/**
 * 模拟浏览器上传文件
 *
 * @param	string	$url			上传地址
 * @param	string	$field			$_FILE['name']
 * @param	string	$targetName		文件名（此名字只为统一admin、imed文件名统一）
 * @param	string	$targetFile		文件绝对路径
 * @return   string    过滤后输出的字符
 */
function curlPost($url,$field,$targetName,$targetFile){
	//echo $targetFile;exit;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);

	$post = array(
		$field			=>'@'.$targetFile,
		'targetName'	=> $targetName
	);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
	$response = curl_exec($ch);
	//var_dump($response);
	//var_dump($error);exit;
	if ($error = curl_error($ch) ) {
		   die($error);
	}

	curl_close($ch);
}

/** 
* @name thum    缩略图函数 
* @param    string   $img_name   图片路径 
* @param    int		 $max_width  略图最大宽度 
* @param    int      $max_height 略图最大高度 
* @param    string   $suffix 略图后缀(如"img_x.jpg"代表小图,"img_m.jpg"代表中图,"img_l.jpg"代表大图)
* @param    string	 $path 图片路径 
* @return   void 
*/  
function getThumbPic($img_name,$max_width,$max_height,$suffix=''){  
	$img_infos=getimagesize($img_name);  
	$img_height=$img_infos[0];//图片高  
	$img_width=$img_infos[1];//图片宽  
	$img_extension='';//图片后缀名  
	
	switch($img_infos[2]){  
		case 1:  
			$img_extension='gif';  
		break;  
		case 2:  
			$img_extension='jpeg';  
		break;  
		case 3:  
			$img_extension='png';  
		break;  
		default:  
			$img_extension='jpeg';  
		break;  
	}  
	
	$new_img_size=getThumbSize($img_width,$img_height,$max_width,$max_height);//新的图片尺寸  

	//var_dump($img_infos);
	//print_r($new_img_size);  
	//die('test');  
	$img_func='';//函数名称  
	$img_handle='';//图片句柄  
	$thum_handle='';//略图图片句柄  
	
	switch($img_extension){  
		case 'jpg':  
			$img_handle=imagecreatefromjpeg($img_name);  
			$img_func='imagejpeg';  
		break;  
		case 'jpeg':  
			$img_handle=imagecreatefromjpeg($img_name);  
			$img_func='imagejpeg';  
		break;  
		case 'png':  
			$img_handle=imagecreatefrompng($img_name);  
			$img_func='imagepng';  
		break;  
		case 'gif':  
			$img_handle=imagecreatefromgif($img_name);  
			$img_func='imagegif';  
		break;  
		default:  
			$img_handle=imagecreatefromjpeg($img_name);  
			$img_func='imagejpeg';  
		break;  
	}  

	$quality=100;//图片质量  
	if($img_func==='imagepng' && (str_replace('.', '', PHP_VERSION)>= 512)){//针对php版本大于5.12参数变化后的处理情况  
		$quality=9;  
	}   

	$thum_handle=imagecreatetruecolor($new_img_size['height'],$new_img_size['width']);  
	if(function_exists('imagecopyresampled')){  
		imagecopyresampled($thum_handle,$img_handle, 0, 0, 0, 0,$new_img_size['height'],$new_img_size['width'],$img_height,$img_width);  
	}else{  
		imagecopyresized($thum_handle,$img_handle, 0, 0, 0, 0,$new_img_size['height'],$new_img_size['width'],$img_height,$img_width);  
	}
	
	call_user_func_array($img_func,array($thum_handle,getThumbName($img_name,$suffix),$quality));

	imagedestroy($thum_handle);//清除句柄  
	imagedestroy($img_handle);//清除句柄  
}  
  
/** 
* @name get_thum_size 获得缩略图的尺寸 
* @param    $width  图片宽 
* @param    $height 图片高 
* @param    $max_width 最大宽度 
* @param    $maxHeight 最大高度 
* @param    array $size; 
*/  
function getThumbSize($width,$height,$max_width,$max_height){  
	$now_width=$width;//现在的宽度  
	$now_height=$height;//现在的高度  
	$size=array();  
	if($now_width>$max_width){//如果现在宽度大于最大宽度  
		$now_height*=number_format($max_width/$width,4);
		$now_width= $max_width;  
	}  
	if($now_height>$max_height){//如果现在高度大于最大高度  
		$now_width*=number_format($max_height/$now_height,4);
		$now_height=$max_height;  
	}
	$size['width']=floor($now_width);  
	$size['height']=floor($now_height);//var_dump($size);exit;
	return $size;  
}  
  
/** 
*@ name get_thum_name 获得略图的名称(在大图基础加_x) 
*/  
function getThumbName($img_name,$suffix){  
	$str=explode('.',$img_name);
	return $str[0].'_'.$suffix.'.'.$str[1];
}