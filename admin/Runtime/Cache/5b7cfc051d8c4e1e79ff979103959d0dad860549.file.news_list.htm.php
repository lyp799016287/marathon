<?php /* Smarty version Smarty-3.1.6, created on 2015-06-23 17:51:09
         compiled from "/yzserver/www/admin_imed_me/admin/Paper/View/News/news_list.htm" */ ?>
<?php /*%%SmartyHeaderCode:10331328525583d2252656b5-16014584%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b7cfc051d8c4e1e79ff979103959d0dad860549' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Paper/View/News/news_list.htm',
      1 => 1435052975,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10331328525583d2252656b5-16014584',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5583d22558da5',
  'variables' => 
  array (
    'list' => 0,
    'foo' => 0,
    'total' => 0,
    'current' => 0,
    'total_num' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5583d22558da5')) {function content_5583d22558da5($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="cleartype" content="on">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" media="all" href="/admin.css" />
<title>原创文章</title>
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
				<!--<th class="check"><input type="checkbox" id='checked' /><label for="checked">全选</label></th>-->
				<th class="label" width="10%">分类</th>
				<th class="title" width="40%">标题</th>
				<th class="source">发布时间</th>
				<th class="level" width="20%">优先级</th>
				<th class="status" width="10%">状态</th>
				<th class="op" width="20%">操作</th>
			</tr>

			<?php  $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['foo']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['foo']->key => $_smarty_tpl->tpl_vars['foo']->value){
$_smarty_tpl->tpl_vars['foo']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['foo']->key;
?>
			<tr class="data">
				<!--<td><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" name="info_id"></td>-->
				<td class="label"><?php echo $_smarty_tpl->tpl_vars['foo']->value['category'];?>
</td>
				<td class="title"><?php echo $_smarty_tpl->tpl_vars['foo']->value['title'];?>
</td>
				<td class="source"><?php echo $_smarty_tpl->tpl_vars['foo']->value['pub_date'];?>
</td>
				<td class="level"><?php echo $_smarty_tpl->tpl_vars['foo']->value['level'];?>
</td>
				<td class="status"><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==4){?>删除<?php }elseif($_smarty_tpl->tpl_vars['foo']->value['status']==3){?>已发布<?php }elseif($_smarty_tpl->tpl_vars['foo']->value['status']==1){?>已审核未发布<?php }else{ ?>未审核<?php }?></td>
				<td class="op"><a href="/paper/news/newsprev?id=<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" target="_blank" class="prevBtn">预览</a> <?php if ($_smarty_tpl->tpl_vars['foo']->value['status']!=3){?>| <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="editBtn">编辑</a><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==0){?>| <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="chkBtn">审核</a><?php }?><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==1){?> | <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="pubBtn">发布</a><?php }?> | <a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="delBtn">删除</a><?php }?></td>
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
			<a href="/paper/news/newslist?page=<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
</a>
			<?php }?>
			<?php endfor; endif; ?>
		</div>
	</div>
</div>
	

<script type="text/javascript">
var paperPage = {};
	paperPage.doneAction = function(){
		
		$(".editBtn").click(function(){
			console.log($(this).attr('sValue'));
			window.location.href = "/paper/news/newsshow?id="+$(this).attr('sValue');
		});

		$(".delBtn").click(function(){
			console.log($(this).attr('sValue'));
			paperPage.delAction($(this).attr('sValue'));
		});

		$(".chkBtn").click(function(){
			console.log($(this).attr('sValue'));
			paperPage.chkAction($(this).attr('sValue'));
		});

		/*$(".prevBtn").click(function(){
			console.log($(this).attr('sValue'));
			window.open('/paper/news/newsprev?id='+$(this).attr('sValue'));
		});*/

		$(".pubBtn").click(function(){
			console.log($(this).attr('sValue'));
			paperPage.pubAction($(this).attr('sValue'));
		})

		$("#btn_add").click(function(){
			window.location.href = "/paper/news/newsadd";
		})
	}

	paperPage.chkAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/paper/news/newschk/',
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

	paperPage.delAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/paper/news/newsdel/',
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

	paperPage.pubAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/paper/news/newspub/',
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

	paperPage.init = function(){
		paperPage.doneAction();
	}

paperPage.init();
</script>	
</body>
</html><?php }} ?>