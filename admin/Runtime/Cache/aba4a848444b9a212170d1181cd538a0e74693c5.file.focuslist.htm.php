<?php /* Smarty version Smarty-3.1.6, created on 2015-06-19 16:27:39
         compiled from "/yzserver/www/admin_imed_me/admin/Manage/View/Focus/focuslist.htm" */ ?>
<?php /*%%SmartyHeaderCode:3555587405583d27b1efa10-89703256%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aba4a848444b9a212170d1181cd538a0e74693c5' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Manage/View/Focus/focuslist.htm',
      1 => 1434511349,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3555587405583d27b1efa10-89703256',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list' => 0,
    'foo' => 0,
    'total' => 0,
    'current' => 0,
    'total_num' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5583d27b4838e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5583d27b4838e')) {function content_5583d27b4838e($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/yzserver/www/ThinkPHPLIB/Library/Vendor/Smarty/plugins/modifier.date_format.php';
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="cleartype" content="on">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" media="all" href="/admin.css" />
<title>首页轮播</title>
<script type="text/javascript" src="/res/js/jquery.min.js"></script>
<script type="text/javascript" src="/res/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/res/js/jquery.md5.js"></script>
<script type="text/javascript" src="/res/js/loginstatus.js"></script>
<style>
table {
	font-size:14px;
	text-align:center;
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
	<header>
		<!--<label>状态：<select id="status" name="status"><option value="0">--全部--</option>
				<option value="1">有效</option>
				<option value="2">无效</option>
			</select>	
		</label>
		<label><button id="btn_search">搜 索</button></label>-->
		<label><button id="btn_add">新 增</button></label>
	</header>
	<hr />
	<div id="content">
		<table id='datalist'>
			<tr>
				<th class="check"><input type="checkbox" id='checked' /><label for="checked">全选</label></th>
				<th class="imageUrl">图片</th>
				<th class="url">链接</th>
				<th class="title">标题</th>
				<th class="start">开始时间</th>
				<th class="end">结束时间</th>
				<th class="level">优先级</th>
				<th class="status">状态</th>
				<th class="op">操作</th>
			</tr>

			<?php  $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['foo']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['foo']->key => $_smarty_tpl->tpl_vars['foo']->value){
$_smarty_tpl->tpl_vars['foo']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['foo']->key;
?>
			<tr class="data">
				<td><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" name="info_id"></td>
				<td class="imageUrl"><img width="350" height="100" src="/res/<?php echo $_smarty_tpl->tpl_vars['foo']->value['imgurl'];?>
" /></td>
				<td class="url"><?php echo $_smarty_tpl->tpl_vars['foo']->value['url'];?>
</td>
				<td class="title"><?php echo $_smarty_tpl->tpl_vars['foo']->value['title'];?>
</td>
				<td class="start"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['foo']->value['start_time'],"%Y-%m-%d");?>
</td>
				<td class="end"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['foo']->value['end_time'],"%Y-%m-%d");?>
</td>
				<td class="level"><?php echo $_smarty_tpl->tpl_vars['foo']->value['level'];?>
</td>
				<td class="status"><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==2){?>删除<?php }elseif($_smarty_tpl->tpl_vars['foo']->value['status']==1){?>已发布<?php }else{ ?>未审核<?php }?></td>
				<td class="op"><a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="editBtn">编辑</a><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==0){?> | <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="pubBtn">发布</a><?php }?> | <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="delBtn">删除</a></td>
			</tr>
			<?php } ?>
		</table>
		<div id="pager">
			<span class="total">共<?php echo $_smarty_tpl->tpl_vars['total']->value;?>
条记录/第<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['total_num']->value;?>
页</span>
			<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['total'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['total']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['name'] = 'total';
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['total_num']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['total']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['total']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['total']['total']);
?>
			<?php if ($_smarty_tpl->tpl_vars['current']->value==$_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1){?>
			<span><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
</span>
			<?php }else{ ?>
			<a href="/manage/focus/focuslist?page=<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
</a>
			<?php }?>
			<?php endfor; endif; ?>
		</div>
	</div>
</div>
	

<script type="text/javascript">
var focusPage = {};
	focusPage.doneAction = function(){
		
		$(".editBtn").click(function(){
			console.log($(this).attr('sValue'));
			window.location.href = "/manage/focus/focusshow?id="+$(this).attr('sValue');
		});

		$(".delBtn").click(function(){
			console.log($(this).attr('sValue'));
			focusPage.delAction($(this).attr('sValue'));
		});

		$(".pubBtn").click(function(){
			console.log($(this).attr('sValue'));
			focusPage.pubAction($(this).attr('sValue'));
		})

		$("#btn_add").click(function(){
			window.location.href = "/manage/focus/focusadd";
		})
	}

	focusPage.delAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/manage/focus/focusdel/',
			data : data,
			dataType : 'json',
			success: function(rdata) {
				if(rdata.code == 1){
					alert(rdata.message);
					window.location.reload();
				}
			},
			error: function() {
				alert("接口调用失败");
			}
		});
	}

	focusPage.pubAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/manage/focus/focuspub/',
			data : data,
			dataType : 'json',
			success: function(rdata) {
				if(rdata.code == 1){
					alert(rdata.message);
					window.location.reload();
				}
			},
			error: function() {
				alert("接口调用失败");
			}
		});
	}

	focusPage.init = function(){
		focusPage.doneAction();
	}

focusPage.init();
</script>	
</body>
</html><?php }} ?>