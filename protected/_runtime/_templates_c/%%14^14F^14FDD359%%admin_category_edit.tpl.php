<?php /* Smarty version 2.6.26, created on 2010-10-01 18:30:37
         compiled from system:gallery/admin_category_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:gallery/admin_category_edit.tpl', 5, false),)), $this); ?>
<h2><?php if ($this->_tpl_vars['form']->id->getValue()): ?>Правка<?php else: ?>Создание<?php endif; ?> категории галереи</h2>
<?php echo $this->_tpl_vars['form']->html(); ?>

<br />
<p>
    <a <?php echo smarty_function_href(array(), $this);?>
>&laquo; Список категорий галерея</a>
    <?php if ($this->_tpl_vars['form']->id->getValue()): ?>| <a <?php echo smarty_function_href(array('viewcat' => $this->_tpl_vars['form']->id), $this);?>
>Изображения в галереи &raquo;</a><?php endif; ?>
</p>