<?php /* Smarty version 2.6.26, created on 2011-01-05 15:55:58
         compiled from system:news/item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'system:news/item.tpl', 1, false),array('modifier', 'default', 'system:news/item.tpl', 2, false),)), $this); ?>
<p><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['news']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</strong></p>
<h2><?php echo ((is_array($_tmp=@$this->_tpl_vars['news']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['news']['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['news']['name'])); ?>
</h2>
<div>
<?php echo $this->_tpl_vars['news']['text']; ?>

</div>