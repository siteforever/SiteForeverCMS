<?php /* Smarty version 2.6.26, created on 2010-09-24 17:24:17
         compiled from system:news/admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:news/admin.tpl', 1, false),array('function', 'icon', 'system:news/admin.tpl', 17, false),array('modifier', 'truncate', 'system:news/admin.tpl', 14, false),array('modifier', 'date_format', 'system:news/admin.tpl', 15, false),)), $this); ?>
<p><a <?php echo smarty_function_href(array('url' => "admin/news"), $this);?>
>Категории материалов</a>
&gt; <?php echo $this->_tpl_vars['cat']['name']; ?>

&gt; <a <?php echo smarty_function_href(array('url' => "admin/news",'newsedit' => '0','cat' => $this->_tpl_vars['cat']['id']), $this);?>
>Создать материал</a></p>
<table class="dataset fullWidth">
<tr>
    <th>id</th>
    <th>Наименование</th>
    <th>Дата</th>
    <th>Свойства</th>
</tr>
<?php $_from = $this->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
    <td><?php echo $this->_tpl_vars['item']['id']; ?>
</td>
    <td><a <?php echo smarty_function_href(array('url' => "admin/news",'newsedit' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 100) : smarty_modifier_truncate($_tmp, 100)); ?>
</a></td>
    <td><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</td>
    <td>
        <?php if ($this->_tpl_vars['item']['hidden']): ?><?php echo smarty_function_icon(array('name' => 'lightbulb_off','title' => "Выкл"), $this);?>
<?php else: ?><?php echo smarty_function_icon(array('name' => 'lightbulb','title' => "Вкл"), $this);?>
<?php endif; ?>
        <?php if ($this->_tpl_vars['item']['protected']): ?><?php echo smarty_function_icon(array('name' => 'lock','title' => "Закрыто"), $this);?>
<?php endif; ?>
    </td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="4">Ничего не найдено</td>
</tr>
<?php endif; unset($_from); ?>
</table>
<p>&nbsp;</p>
<?php echo $this->_tpl_vars['paging']['html']; ?>