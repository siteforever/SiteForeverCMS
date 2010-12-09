<?php /* Smarty version 2.6.26, created on 2010-10-14 17:30:12
         compiled from system:gallery/admin_category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:gallery/admin_category.tpl', 12, false),array('function', 'icon', 'system:gallery/admin_category.tpl', 13, false),)), $this); ?>
<table class="dataset fullWidth">
<tr>
    <th width="30"></th>
    <th width="50%">Наименование</th>
    <th width="25%">Средняя картинка</th>
    <th width="25%">Миниатюра</th>
</tr>
<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
<tr>
    <td>#<?php echo $this->_tpl_vars['cat']['id']; ?>
</td>
    <td>
        <a <?php echo smarty_function_href(array('viewcat' => $this->_tpl_vars['cat']['id']), $this);?>
><?php echo $this->_tpl_vars['cat']['name']; ?>
</a>
        <a <?php echo smarty_function_href(array('editcat' => $this->_tpl_vars['cat']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>
</a>
        <a <?php echo smarty_function_href(array('delcat' => $this->_tpl_vars['cat']['id']), $this);?>
 class="do_delete"><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</a>
        </td>
    <td><?php echo $this->_tpl_vars['cat']['middle_width']; ?>
 x <?php echo $this->_tpl_vars['cat']['middle_height']; ?>
</td>
    <td><?php echo $this->_tpl_vars['cat']['thumb_width']; ?>
 x <?php echo $this->_tpl_vars['cat']['thumb_height']; ?>
</td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="3">Ничего не найдено</td>
</tr>
<?php endif; unset($_from); ?>
</table>
<p><a <?php echo smarty_function_href(array('newcat' => '1'), $this);?>
>Добавить категорию</a></p>