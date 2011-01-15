<?php /* Smarty version 2.6.26, created on 2011-01-15 16:43:14
         compiled from system:news/catedit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:news/catedit.tpl', 2, false),)), $this); ?>
<p>
    <a <?php echo smarty_function_href(array('url' => "admin/news"), $this);?>
>Раздел новости</a>
    &gt; Правка раздела
    &gt; <a <?php echo smarty_function_href(array('url' => "admin/news",'catid' => $this->_tpl_vars['form']->id), $this);?>
>Перейти</a>
</p>

<?php echo $this->_tpl_vars['form']->html(); ?>
