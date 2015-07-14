<?php /* Smarty version Smarty-3.1.6, created on 2015-06-23 17:40:42
         compiled from "/yzserver/www/admin_imed_me/admin/Paper/View/News/news_edit.htm" */ ?>
<?php /*%%SmartyHeaderCode:150041782255890cebf34c02-99620218%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e97f9ed57b9fe5e217160424f5dc3b51e6d16b5' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Paper/View/News/news_edit.htm',
      1 => 1435052135,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '150041782255890cebf34c02-99620218',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_55890cec3155d',
  'variables' => 
  array (
    'data' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55890cec3155d')) {function content_55890cec3155d($_smarty_tpl) {?><!DOCTYPE html>
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
<title>原创文章（编辑）</title>
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
			<input type="hidden" id="pid" name="pid" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
" />
			<div><label style="color:red;">*号必填</label></div>
			<div>
			<label>分&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;类：<select name="label_type" id="label_type" style="width:180px;">
					<!--<option value="1" <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==1){?>selected="selected"<?php }?>>指南</option>
					<option value="2" <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==2){?>selected="selected"<?php }?>>临床研究</option>
					<option value="3" <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==3){?>selected="selected"<?php }?>>病例分析</option>-->
					<option value='1' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==1){?>selected="selected"<?php }?>>推广</option>
					<option value='2' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==2){?>selected="selected"<?php }?>>深度解读</option>
					<option value='3' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==3){?>selected="selected"<?php }?>>指南</option>
					<option value='4' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==4){?>selected="selected"<?php }?>>临床研究</option>
					<option value='5' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==5){?>selected="selected"<?php }?>>病例分析</option>
					<!--<option value='6' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==6){?>selected="selected"<?php }?>></option>-->
					<option value='7' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==7){?>selected="selected"<?php }?>>推荐</option>
					<option value='8' <?php if ($_smarty_tpl->tpl_vars['data']->value['category']==8){?>selected="selected"<?php }?>>热点</option>
					</select>&nbsp;&nbsp;<span style="color:red;">*</span>
			</label>
			</div>
			<div>
			<label>标&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;题：<input type="text" id="title" name="title" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['title'];?>
" style="width:50%" />&nbsp;&nbsp;<span style="color:red;">*</span></label>
			</div>
			<div>
			<label>副&nbsp;标&nbsp;题：<input type="text" id="sub_title" name="sub_title" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['sub_title'];?>
" style="width:50%" /></label>
			</div>
			<div>
				<label>发布时间：<input type="text" id="pub_date" name="pub_date" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['pub_date'];?>
" />&nbsp;&nbsp;<span style="color:red;">*</span></label>
			</div>
			<div>
			<label>关&nbsp;键&nbsp;字：<input type="text" id="keys" name="keys" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['keys'];?>
" style="width:50%" />&nbsp;&nbsp;(多个关键字请以逗号,分隔)</label>
			</div>
			<div>
				<label>原文链接：<input type="text" id="url" name="url" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['src_url'];?>
" style="width:50%" /></label>
			</div>
			<div>
				<label>来&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;源：<input type="text" id="source" name="source" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['sourece'];?>
" style="width:50%" /></label>
			</div>
			<div>
				<label>优&nbsp;先&nbsp;级：<select name="level_num" id="level_num" style="width:180px;">
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['data']->value['level']==1){?>selected="selected"<?php }?>>1</option>
					<option value="2" <?php if ($_smarty_tpl->tpl_vars['data']->value['level']==2){?>selected="selected"<?php }?>>2</option>
					<option value="3" <?php if ($_smarty_tpl->tpl_vars['data']->value['level']==3){?>selected="selected"<?php }?>>3</option>
					</select>
				</label>
			</div>
			<br />
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
				<label><img id="img1" <?php if ($_smarty_tpl->tpl_vars['data']->value['img_url']){?>src="/res/News/<?php echo $_smarty_tpl->tpl_vars['data']->value['img_url'];?>
"<?php }?> /><input type="hidden" id="img_val" /></label>
				<br />
			</div>
			
			<div id="oppanel">
				<input type="button" id='btn_save' value="保存" />
				<input type="button" id='btn_preview' value="预览" />
				<!--<input type="button" id='btn_publish' value="发布" />-->
			</div>
		</form>
			
		
	</div>
	</div>
<script type="text/javascript">
	
$().ready(function(){

var paperEdit = {}; //namespace

paperEdit.init = function(){

	paperEdit.main_editor = UE.getEditor('main',{
		imageUrl: '/paper/news/uploadeditorimg'
	});

	
	paperEdit.main_editor.ready(function(){
		paperEdit.main_editor.setContent('<?php echo $_smarty_tpl->tpl_vars['data']->value['content'];?>
');
	});

	$( "#pub_date" ).datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	$("#uploadBtn").click(function () { 
		paperEdit.ajaxFileUpload();
	});

	$('#btn_save').click(function(){
		paperEdit.saveNews();
	});

	$('#btn_preview').click(function(){
		//window.open('/paper/news/newsprev?id='+$("#pid").val());
		paperEdit.preNews();
	});

	$('#btn_publish').click(function(){
		paperEdit.pubNews();
	});
}

paperEdit.getParams = function(){
	var params = {};

	params['id'] = $("#pid").val();
	params['title'] = $("#title").val();
	params['type'] = $("#label_type").val();
	params['keys'] = $("#keys").val();
	params['url'] = $("#url").val();
	params['level'] = $("#level_num").val();
	params['source'] = $("#source").val();
	params['img'] = $("#img_val").val();
	params['sub_title'] = $("#sub_title").val();
	params['content'] = paperEdit.main_editor.getContent();
	params['action'] = 'edit';
	params['pub_date'] = $("#pub_date").val();

	console.log(params);

	return params;
}


paperEdit.saveNews = function(){
	
	var params = paperEdit.getParams();

	if($.trim(params['title']) == ''){
		alert('标题不能为空');
		return false;
	}

	if($.trim(params['content']) == ""){
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
				window.location.href ="/paper/news/newslist";
				//window.location.href = document.referrer;
			}else{
				alert(rdata.message);
			}
		},
		error: function() {
			alert("接口调用失败！");
		}
	});
};

paperEdit.preNews = function(){
	
	var params = paperEdit.getParams();

	if($.trim(params['title']) == ''){
		alert('标题不能为空');
		return false;
	}

	if($.trim(params['content']) == ""){
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
				//alert(rdata.message);
				window.open('/paper/news/newsprev?id='+params['id']);
			}else{
				alert(rdata.message);
			}
		},
		error: function() {
			alert("接口调用失败！");
		}
	});
}

paperEdit.pubNews = function(){
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


paperEdit.ajaxFileUpload = function() {
	$.ajaxFileUpload({
		url: '/paper/news/uploadimg', //用于文件上传的服务器端请求地址
		secureuri: false, //一般设置为false
		fileElementId: 'uploadify', //文件上传空间的id属性
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

paperEdit.init();

});

</script>
</body>
</html><?php }} ?>