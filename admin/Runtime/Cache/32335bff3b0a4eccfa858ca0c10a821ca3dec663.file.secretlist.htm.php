<?php /* Smarty version Smarty-3.1.6, created on 2015-06-19 16:28:30
         compiled from "/yzserver/www/admin_imed_me/admin/Manage/View/Secret/secretlist.htm" */ ?>
<?php /*%%SmartyHeaderCode:18392132345583d2ae242ff3-98991041%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32335bff3b0a4eccfa858ca0c10a821ca3dec663' => 
    array (
      0 => '/yzserver/www/admin_imed_me/admin/Manage/View/Secret/secretlist.htm',
      1 => 1433835393,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18392132345583d2ae242ff3-98991041',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'keyword' => 0,
    'bgn_date' => 0,
    'end_date' => 0,
    'status' => 0,
    'list' => 0,
    'foo' => 0,
    'total' => 0,
    'current' => 0,
    'total_num' => 0,
    'querypara' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5583d2ae5721a',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5583d2ae5721a')) {function content_5583d2ae5721a($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/yzserver/www/ThinkPHPLIB/Library/Vendor/Smarty/plugins/modifier.date_format.php';
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
<title>秘密列表</title>
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
		<form action="/manage/secret/secretList" method="post" id='form_search' onsubmit="return false;">
			<label>关键词：<input type="text" name='keyword' id="keyword"  value="<?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
"/></label> <label>时间：<input
				type="text" name='bgn_date' id="bgn_date" value="<?php echo $_smarty_tpl->tpl_vars['bgn_date']->value;?>
"/>~<input type="text" name='end_date' id="end_date" value="<?php echo $_smarty_tpl->tpl_vars['end_date']->value;?>
"/></label>
			<label>状态：<select id="status" name="status"><option value=''>--全部--</option>
                <option value='0' <?php if ($_smarty_tpl->tpl_vars['status']->value=='0'){?>selected<?php }?>>待审核</option>
				<option value='1' <?php if ($_smarty_tpl->tpl_vars['status']->value=='1'){?>selected<?php }?>>已发布</option>
				<option value='2' <?php if ($_smarty_tpl->tpl_vars['status']->value=='2'){?>selected<?php }?>>作废</option>
				<option value='3' <?php if ($_smarty_tpl->tpl_vars['status']->value=='3'){?>selected<?php }?>>已删除</option>
			</select>
			</label>
			<label><button id="btn_search">搜 索</button></label>
			<label><button id="btn_add">添 加</button></label>
            
            
            
		</form>

	</header>
	<hr />
	<div id="content">
		<table id='datalist'>
			<tr>
				<th class="check"><input type="checkbox" id='checked' /><label for="checked">全选</label></th>
				<th class="title">主题ID</th>
				<th width="30%" class="title">主题内容</th>
				<th class="start">秘密发言类型</th>
				<th class="end"><span class="level">秘密用户</span></th>
				<th class="level">更新时间</th>
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
				<td class="title"><?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
</td>
				<td class="title"><?php echo $_smarty_tpl->tpl_vars['foo']->value['content'];?>
</td>
				<td class="start"><?php if ($_smarty_tpl->tpl_vars['foo']->value['type']==0){?>匿名发布<?php }else{ ?>推送主题<?php }?></td>
				<td class="end"><?php if ($_smarty_tpl->tpl_vars['foo']->value['user_id']==0){?>系统用户<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['foo']->value['name'];?>
 | <?php echo $_smarty_tpl->tpl_vars['foo']->value['mobile'];?>
<?php }?></td>
				<td class="start"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['foo']->value['create_time'],"%Y-%m-%d");?>
</td>
				<td class="status"><?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==0){?>待审核<?php }elseif($_smarty_tpl->tpl_vars['foo']->value['status']==1){?>已发布<?php }else{ ?>作废<?php }?></td>
				<td class="op">
                <?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==1&&$_smarty_tpl->tpl_vars['foo']->value['user_id']==0){?>
                	<a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="statusBtn0">未审</a> 
                <?php }elseif($_smarty_tpl->tpl_vars['foo']->value['status']==2||($_smarty_tpl->tpl_vars['foo']->value['user_id']==0&&$_smarty_tpl->tpl_vars['foo']->value['status']==0)){?>
               		<a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="statusBtn1">发布</a>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['foo']->value['status']==1||($_smarty_tpl->tpl_vars['foo']->value['user_id']==0&&$_smarty_tpl->tpl_vars['foo']->value['status']==1)){?>
                	<a href="javascript:;" sValue="<?php echo $_smarty_tpl->tpl_vars['foo']->value['id'];?>
" class="statusBtn2">作废</a>
                <?php }?>
               
                
                
                </td>
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
			<a href="/manage/secret/secretlist?page=<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
<?php echo $_smarty_tpl->tpl_vars['querypara']->value;?>
"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['total']['index']+1;?>
</a>
			<?php }?>
			<?php endfor; endif; ?>
		</div>
	</div>
</div>
	

<script type="text/javascript">
var secretPage = {};
	secretPage.doneAction = function(){
		
		$(".statusBtn0").click(function(){
			console.log($(this).attr('sValue'));
			secretPage.statusAction($(this).attr('sValue'),0);			
		});

		$(".statusBtn1").click(function(){
			console.log($(this).attr('sValue'));
			secretPage.statusAction($(this).attr('sValue'),1);						
		});
		
		$(".statusBtn2").click(function(){
			console.log($(this).attr('sValue'));
			secretPage.statusAction($(this).attr('sValue'),2);						
		});
		
		$("#btn_add").click(function(){
			window.location.href="/manage/secret/secretadd";
		});
						
	}

	secretPage.statusAction = function(id,status){
		var data = {
			'id' : id,
			'status' : status
		};
		
		$.ajax({
			type : 'POST',
			url : '/manage/secret/SecretStatus/',
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

	secretPage.delAction = function(id){
		var data = {
			'id' : id
		};

		$.ajax({
			type : 'POST',
			url : '/manage/secret/secretDel/',
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
	
	secretPage.search=function(gotopage,type){

			var keyword = $('#keyword').val();
			var bgn_date = $('#bgn_date').val();
			var end_date = $('#end_date').val();
			var status = $('#status').val();
			if(gotopage){
				page=gotopage;
			}else{
				page=0;
			}


		};	

	secretPage.init = function(){
		secretPage.doneAction();
		$('#btn_search').click(function(){
			form_search.submit();
			//btn_flag=1;
			//secretPage.search();
		});
	}

secretPage.init();
</script>	
</body>
</html><?php }} ?>