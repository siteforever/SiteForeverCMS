<?php /* Smarty version 2.6.26, created on 2010-09-24 17:24:21
         compiled from system:news/catedit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:news/catedit.tpl', 1, false),)), $this); ?>
<p><a <?php echo smarty_function_href(array('url' => "admin/news"), $this);?>
>Раздел новости</a> &gt; Правка раздела</p>

<?php echo $this->_tpl_vars['form']->html(); ?>
