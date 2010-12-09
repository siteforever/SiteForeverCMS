<?php /* Smarty version 2.6.26, created on 2010-10-14 18:40:09
         compiled from system:catalog/admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'icon', 'system:catalog/admin.tpl', 16, false),array('function', 'href', 'system:catalog/admin.tpl', 17, false),)), $this); ?>

<?php echo $this->_tpl_vars['breadcrumbs']; ?>

<br />
<table class="catalog_data dataset fullWidth">
<tr>
    <th colspan="3">Наименование</th>
    <th width="100">Подразделов/Артикул</th>
    <th width="120">Действия</th>
</tr>
<?php $_from = $this->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr <?php if ($this->_tpl_vars['item']['cat']): ?>rel="<?php echo $this->_tpl_vars['item']['id']; ?>
" class="cat"<?php endif; ?>>
    <td width="20"><input type="checkbox" class="checkbox" name="move[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
"></td>
    <td width="30" class="right"><?php echo $this->_tpl_vars['item']['id']; ?>
</td>
    <td>
        <?php if ($this->_tpl_vars['item']['cat']): ?>
            <?php echo smarty_function_icon(array('name' => 'folder','title' => "Каталог"), $this);?>

            <a <?php echo smarty_function_href(array('url' => "admin/catalog",'part' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo $this->_tpl_vars['item']['name']; ?>
</a>
        <?php else: ?>
            <?php echo smarty_function_icon(array('name' => 'page','title' => "Товар"), $this);?>

            <a <?php echo smarty_function_href(array('url' => "admin/catalog",'edit' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo $this->_tpl_vars['item']['name']; ?>
</a>
        <?php endif; ?>
    </td>
    <td><?php if ($this->_tpl_vars['item']['cat'] == 1): ?><?php echo $this->_tpl_vars['item']['child_count']; ?>
<?php else: ?><?php echo $this->_tpl_vars['item']['articul']; ?>
<?php endif; ?></td>
    <td>
        <a <?php echo smarty_function_href(array('url' => "admin/catalog",'edit' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'pencil','title' => "Править"), $this);?>
</a>
        <?php if ($this->_tpl_vars['item']['hidden']): ?>
            <a <?php echo smarty_function_href(array('url' => "admin/catalog",'item' => $this->_tpl_vars['item']['id'],'switch' => 'on'), $this);?>
 class="catalog_switch"><?php echo smarty_function_icon(array('name' => 'lightbulb_off','title' => "Включить"), $this);?>
</a>
        <?php else: ?>
            <a <?php echo smarty_function_href(array('url' => "admin/catalog",'item' => $this->_tpl_vars['item']['id'],'switch' => 'off'), $this);?>
 class="catalog_switch"><?php echo smarty_function_icon(array('name' => 'lightbulb','title' => "Выключить"), $this);?>
</a>
        <?php endif; ?>
                <?php if ($this->_tpl_vars['item']['cat'] == 1): ?>
        <a <?php echo smarty_function_href(array('type' => '1','add' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'folder_add','title' => "Добавить подраздел"), $this);?>
</a>
        <a <?php echo smarty_function_href(array('type' => '0','add' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'page_add','title' => "Добавить товар"), $this);?>
</a>
        <?php endif; ?>
        <a <?php echo smarty_function_href(array('del' => $this->_tpl_vars['item']['id']), $this);?>
 class="do_delete"><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</a></td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="5">Пока нет разделов</td>
</tr>
<?php endif; unset($_from); ?>
</table>
<p>
<select id="catalog_move_target">
<?php $_from = $this->_tpl_vars['moving_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option><?php endforeach; endif; unset($_from); ?>
</select>
<button <?php echo smarty_function_href(array('part' => $this->_tpl_vars['parent']['id']), $this);?>
 id="catalog_move_to_category">Переместить</button></p>
<p><?php echo smarty_function_icon(array('name' => 'folder_add','title' => "Добавить раздел"), $this);?>
 <a <?php echo smarty_function_href(array('add' => $this->_tpl_vars['parent']['id'],'type' => '1'), $this);?>
>Добавить раздел</a> |
<?php echo smarty_function_icon(array('name' => 'page_add','title' => "Добавить товар"), $this);?>
 <a <?php echo smarty_function_href(array('add' => $this->_tpl_vars['parent']['id'],'type' => '0'), $this);?>
>Добавить товар</a> |
<?php echo smarty_function_icon(array('name' => 'table','title' => "Прайслист"), $this);?>
 <a <?php echo smarty_function_href(array('price' => 'load'), $this);?>
>Загрузить прайслист</a></p>
<br />
<p><?php echo $this->_tpl_vars['paging']['html']; ?>
</p>