<?php /* Smarty version Smarty-3.1.6, created on 2015-06-23 17:40:46
         compiled from "/yzserver/www/admin_imed_me/admin/Paper/View/News/news.htm" */ ?>
<?php /*%%SmartyHeaderCode:9087751785589098b033524-03914810%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48911d1689867feadac173af0c12dc61726926e2' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Paper/View/News/news.htm',
      1 => 1435052128,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9087751785589098b033524-03914810',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5589098b22b81',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5589098b22b81')) {function content_5589098b22b81($_smarty_tpl) {?><!DOCTYPE html>
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
<title>原创文章（新建）</title>
<script type="text/javascript" src="/res/js/jquery.min.js"></script>
<script type="text/javascript" src="/res/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/res/Ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="/res/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/res/js/jquery.md5.js"></script>
<script type="text/javascript" src="/res/js/loginstatus.js"></script>
<script type="text/javascript" src="/res/JqueryUI/jquery-ui.min.js"></script>
<script type="text/javascript" src="/res/Uploadify/ajaxfileupload.js"></script>
<style>

#oppanel input[type="button"] {
    display: inline;
}
</style>
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
		<form id="form_detail" method="post">
			<div><label style="color:red;">*号必填</label></div>
			<div>
			<label>分&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;类：<select name="label_type" id="label_type" style="width:180px;">
					<!--<option value="1">指南</option>
					<option value="2">临床研究</option>
					<option value="3">病例分析</option>-->
					<option value='1'>推广</option>
					<option value='2'>深度解读</option>
					<option value='3'>指南</option>
					<option value='4'>临床研究</option>
					<option value='5'>病例分析</option>
					<!--<option value='6'></option>-->
					<option value='7'>推荐</option>
					<option value='8'>热点</option>
					</select>&nbsp;&nbsp;<span style="color:red;">*</span>
			</label>
			</div>
			<div>
			<label>标&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;题：<input type="text" id="title" name="title" style="width:50%" />&nbsp;&nbsp;<span style="color:red;">*</span></label>
			</div>
			<div>
			<label>副&nbsp;标&nbsp;题：<input type="text" id="sub_title" name="sub_title" style="width:50%" /></label>
			</div>
			<div>
				<label>发布时间：<input type="text" id="pub_date" name="pub_date" />&nbsp;&nbsp;<span style="color:red;">*</span></label>
			</div>
			<div>
			<label>关&nbsp;键&nbsp;字：<input type="text" id="keys" name="keys" style="width:50%" />&nbsp;&nbsp;(多个关键字请以逗号,分隔)</label>
			</div>
			<div>
				<label>原文链接：<input type="text" id="url" name="url" style="width:50%" /></label>
			</div>
			<div>
				<label>来&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;源：<input type="text" id="source" name="source" style="width:50%" /></label>
			</div>
			<div>
				<label>优&nbsp;先&nbsp;级：<select name="level_num" id="level_num" style="width:180px;">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					</select>
				</label>
			</div>
			<br /><br />
			<div>
				<label>正&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;文：</label>
				<br />
			</div>
			<div>
				<textarea id="main" ></textarea>
			</div>
			<div>
				<label>图&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;片：</label>
				<label><input type="file" name="uploadify" id="uploadify"></label>
				<label><input type="button" id="uploadBtn" value="上传" /></label>
				<label><img id="img1" /><input type="hidden" id="img_val"></label>
				<br />
			</div>
			
			<div id="oppanel">
				<input type="button" id='btn_save' value="保存" />
				<!--<input type="button" id='btn_preview' value="预览" />-->
				<!--<input type="button" id='btn_publish' value="发布" />-->
			</div>
		</form>
			
		
	</div>
	</div>
<script type="text/javascript">
	
$().ready(function(){

var paperAdd = {}; //namespace
//var param = $.getUrlVars();

paperAdd.init = function(){

	paperAdd.main_editor = UE.getEditor('main',{
		imageUrl: '/paper/news/uploadeditorimg',
		zIndex: 1
	});

	$( "#pub_date" ).datepicker({
		dateFormat: "yy-mm-dd"
	});


	$("#uploadBtn").click(function () { 
		paperAdd.ajaxFileUpload();
	});

	$('#btn_save').click(function(){
		paperAdd.saveNews();
	});
	/*$('#btn_preview').click(function(){
		window.open('/newsDetailPreview.htm?id='+param['id']);
	});*/
	$('#btn_publish').click(function(){
		paperAdd.pubNews();
	});
}

paperAdd.getParams = function(){
	var params = {};

	params['title'] = $("#title").val();
	params['type'] = $("#label_type").val();
	params['keys'] = $("#keys").val();
	params['url'] = $("#url").val();
	params['level'] = $("#level_num").val();
	params['source'] = $("#source").val();
	params['img'] = $("#img_val").val();
	params['sub_title'] = $("#sub_title").val();
	params['content'] = paperAdd.main_editor.getContent();
	params['pub_date'] = $("#pub_date").val();

	console.log(params);

	return params;
}


paperAdd.saveNews = function(){
	
	var params = paperAdd.getParams();

	if($.trim(params['title']) == ''){
		alert('标题不能为空');
		return false;
	}

	if(params['content'] == ""){
		alert('正文不能为空');
		return false;
	}

	$.ajax({
		type : 'POST',
		url : '/paper/news/newspost',
		data: params,
		dataType : 'json',
		success: function(rdata) {
			if(rdata.code == 1){
				alert(rdata.message);
				window.location.href ="/paper/news/newsadd";
			}else{
				alert(rdata.message);
			}
		},
		error: function() {
			alert("接口调用失败！");
		}
	});
};

paperAdd.pubNews = function(){
	/*if(!window.confirm('确定发布到外网吗？')){
		return;
	}
	var main_document = main_editor.getHTML();
	$('#mainBody').val(main_document);
	var summary_document = summary_editor.getHTML();
	$('#summaryBody').val(summary_document);

	$.post('/edit/detail/pubNews',{
		id:$('#id').val(),
		title:$('#title').val(),
		summary:$('#summaryBody').val(),
		content:$('#mainBody').val()
	},function(ret){
		if(ret.code==1){
			alert('发布成功');
			window.location='/newsList.htm'
		}else{
			alert(ret.message);
		}
	});*/
};


paperAdd.ajaxFileUpload = function() {
	$.ajaxFileUpload({
		url: '/paper/news/uploadimg', //用于文件上传的服务器端请求地址
		secureuri: false, //一般设置为false
		fileElementId: 'uploadify', //文件上传空间的id属性  <input type="file" id="file" name="file" />
		dataType: 'json', //返回值类型 一般设置为json
		success: function (rdata, status)  //服务器成功响应处理函数
		{
			if(rdata.code == 1){
				$("#img1").attr("src", '/res/News/'+rdata.data);
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

paperAdd.init();

});

</script>
</body>
</html><?php }} ?>