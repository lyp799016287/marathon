<?php /* Smarty version Smarty-3.1.6, created on 2015-06-23 17:26:39
         compiled from "/yzserver/www/admin_imed_me/admin/Manage/View/Focus/focusadd.htm" */ ?>
<?php /*%%SmartyHeaderCode:833939955589264fd1bd39-50694332%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0b15ee84c8733d1b1b94a81538a8b63712d660b' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Manage/View/Focus/focusadd.htm',
      1 => 1435047433,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '833939955589264fd1bd39-50694332',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5589264fe81ae',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5589264fe81ae')) {function content_5589264fe81ae($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="cleartype" content="on">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" media="all" href="/res/JqueryUI/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="/admin.css" />
<title>轮播图（新增）</title>
<script type="text/javascript" src="/res/js/jquery.min.js"></script>
<script type="text/javascript" src="/res/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/res/js/jquery.md5.js"></script>
<script type="text/javascript" src="/res/js/loginstatus.js"></script>
<script type="text/javascript" src="/res/JqueryUI/jquery-ui.min.js"></script>
<script type="text/javascript" src="/res/Uploadify/ajaxfileupload.js"></script>
</head>
<body>
<div id="left">
	<div id="userinfo">
		<span id='userName'></span>
	</div>
	<nav>
		<?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['menu_path']->value)."/menu.htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</nav>
</div>
<div id="main_body">
	<div id="content">
		<form id="form_detail" method="post" encType="multipart/form-data" target="hidden_frame">
			<div>
				<br />
				<label>标题：<input type="text" id="title" name="title" style="width:50%" /></label>
				<br />
			</div>
			<div>
				<br />
				<label>链接：<input type="text" id="url" name="title" value="http://" style="width:50%" /></label>
				<br />
			</div>
			<div>
				<br />
				<label>图片：</label>
				<label><input type="file" name="uploadify" id="uploadify"></label>
				<label><input type="button" id="uploadBtn" value="上传" /></label>
				<label><img id="img1" /><input type="hidden" id="img_val"></label>
				<br />
			</div>
			<div>
				<br />
				<label>开始时间：<input type="text" id="start_date"></label>
				<label>结束时间：<input type="text" id="end_date"></label>
				<br />
			</div>
			<div>
				<br />
				<label>优先级：<select name="level_num" id="level_num" style="width:180px;">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					</select>
				</label>
				<br />
			</div>
		
			<div>
				<br />
				<input type="button" id='btn_save' value="保存" />
			</div>
		</form>
			
		
	</div>
	</div>
</body>
<script type="text/javascript">
var focusAdd = {};

focusAdd.init = function(){
	$( "#start_date" ).datepicker({
		dateFormat: "yy-mm-dd"
	});
	$( "#end_date" ).datepicker({
		dateFormat: "yy-mm-dd"
	});

	$("#uploadBtn").click(function () {
		ajaxFileUpload();
	});

	$("#btn_save").click(function() {
		querySubmit();
	});
}

function ajaxFileUpload() {
	$.ajaxFileUpload({
		url: '/manage/focus/uploadimg', //用于文件上传的服务器端请求地址
		secureuri: false, //一般设置为false
		fileElementId: 'uploadify', //文件上传空间的id属性  <input type="file" id="file" name="file" />
		dataType: 'json', //返回值类型 一般设置为json
		success: function (rdata, status)  //服务器成功响应处理函数
		{
			console.log(rdata);
			if(rdata.code == 1){
				$("#img1").attr("src", '/res/focuslist/'+rdata.data);
				$("#img_val").val(rdata.data);
			}else{
				alert(rdata.message);
			}
		},
		error: function (data, status, e)//服务器响应失败处理函数
		{
			alert(e);
		}
	});
	return false;
}

function querySubmit(){
	
	var params = {};

	params['title'] = $("#title").val();
	params['url'] = $("#url").val();
	params['img'] = $("#img_val").val();
	params['start_date'] = $("#start_date").val();
	params['end_date'] = $("#end_date").val();
	params['level'] = $("#level_num").val();

	console.log(params);
	if(params['img'] == ''){
		alert("请上传图片");
		return false;
	}

	if($.trim(params['start_date']) == ''){
		alert("请选择开始时间");
		return false;
	}

	if($.trim(params['end_date']) == ''){
		alert("请选择结束时间");
		return false;
	}
	
	$.ajax({
		type : 'POST',
		url : '/manage/focus/focuspost',
		data: params,
		dataType : 'json',
		success: function(rdata) {
			if(rdata.code == 1){
				alert(rdata.message);
				window.location.href ="/manage/focus/focusadd";
			}else{
				alert(rdata.message);
			}
		},
		error: function() {
			alert("接口调用失败！");
		}
	});
}

focusAdd.init();
</script>
</html><?php }} ?>