<?php /* Smarty version 2.6.26, created on 2010-09-24 10:52:14
         compiled from system:catalog/admin_edit.tpl */ ?>
<?php echo $this->_tpl_vars['breadcrumbs']; ?>

<br />
<?php echo $this->_tpl_vars['form']->html(); ?>



<br />
<?php if ($this->_tpl_vars['form']->cat == '0' && $this->_tpl_vars['form']->id): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "system:catgallery/admin_panel.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>