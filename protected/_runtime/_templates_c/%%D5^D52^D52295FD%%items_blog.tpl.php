<?php /* Smarty version 2.6.26, created on 2011-01-15 22:16:17
         compiled from system:news/items_blog.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'system:news/items_blog.tpl', 8, false),array('modifier', 'default', 'system:news/items_blog.tpl', 9, false),array('function', 'href', 'system:news/items_blog.tpl', 9, false),)), $this); ?>
<?php if ($this->_tpl_vars['cat']['show_content']): ?>
    <?php echo $this->_tpl_vars['page']['content']; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['cat']['show_list']): ?>
    <?php $_from = $this->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <div>
        <div><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</strong></div>
        <p><a <?php echo smarty_function_href(array('url' => $this->_tpl_vars['item']['alias'],'doc' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo ((is_array($_tmp=@$this->_tpl_vars['item']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['item']['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['item']['name'])); ?>
</a></p>
        <div><?php echo $this->_tpl_vars['item']['notice']; ?>
</div>
        <div class="right"><a <?php echo smarty_function_href(array('url' => $this->_tpl_vars['item']['alias'],'doc' => $this->_tpl_vars['item']['id']), $this);?>
>подробнее...</a></div>
    </div>
    <?php endforeach; else: ?>
    <div>
        В этом разделе пока нет материалов
    </div>
    <?php endif; unset($_from); ?>

    <hr />
    <?php echo $this->_tpl_vars['paging']['html']; ?>

<?php endif; ?>