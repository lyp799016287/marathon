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