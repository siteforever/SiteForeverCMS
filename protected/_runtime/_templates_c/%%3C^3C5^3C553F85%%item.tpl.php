<?php /* Smarty version 2.6.26, created on 2010-09-12 02:40:19
         compiled from system:news/item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'system:news/item.tpl', 1, false),)), $this); ?>
<p><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['news']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</strong></p>
<div>
<?php echo $this->_tpl_vars['news']['text']; ?>

</div>