<?php /* Smarty version Smarty-3.1.6, created on 2015-06-19 16:28:12
         compiled from "/yzserver/www/admin_imed_me/menu.htm" */ ?>
<?php /*%%SmartyHeaderCode:5824563115583d29c6c6355-81780111%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1cc46af0c59e5e92a15a42b266202a039475e951' => 
    array (
      0 => '/yzserver/www/admin_imed_me/menu.htm',
      1 => 1434080762,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5824563115583d29c6c6355-81780111',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'index' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5583d29c75b67',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5583d29c75b67')) {function content_5583d29c75b67($_smarty_tpl) {?><li <?php if ($_smarty_tpl->tpl_vars['index']->value==1){?>class="current"<?php }?>> 
	<a  href="/newsList.htm?type=0" >待编辑文章</a>
</li>
<li <?php if ($_smarty_tpl->tpl_vars['index']->value==2){?>class="current"<?php }?>>
	<a  href="/paper/news/newslist">原创文章</a>
</li>
<li <?php if ($_smarty_tpl->tpl_vars['index']->value==3){?>class="current"<?php }?>>
	<a  href="/manage/focus/focuslist">首页轮播</a>
</li>
<li <?php if ($_smarty_tpl->tpl_vars['index']->value==4){?>class="current"<?php }?>>
	<a  href="/manage/secret/secretlist">密帖列表</a>
</li>
<?php }} ?>