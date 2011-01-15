<?php /* Smarty version 2.6.26, created on 2011-01-15 02:40:15
         compiled from system:news/catslist.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:news/catslist.tpl', 13, false),array('function', 'icon', 'system:news/catslist.tpl', 14, false),)), $this); ?>
<table class="dataset fullWidth">
<tr>
    <th>ID</th>
    <th>Название</th>
    <th>Описание</th>
    <th>Статей</th>
    <th>Параметры</th>
</tr>
<?php $_from = $this->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
    <td><?php echo $this->_tpl_vars['item']['id']; ?>
</td>
    <td>
        <a <?php echo smarty_function_href(array('url' => "admin/news",'catid' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo $this->_tpl_vars['item']['name']; ?>
</a>
        <a <?php echo smarty_function_href(array('url' => "admin/news",'catedit' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'pencil','title' => "Правка"), $this);?>
</a>
        <a <?php echo smarty_function_href(array('url' => "admin/news",'catdel' => $this->_tpl_vars['item']['id']), $this);?>
><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</a>
    </td>
    <td><?php echo $this->_tpl_vars['item']['description']; ?>
</td>
    <td><?php echo $this->_tpl_vars['item']['news_count']; ?>
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
    <td colspan="5">Ничего не найдено</td>
</tr>
<?php endif; unset($_from); ?>
</table>
<p></p>
<p><a <?php echo smarty_function_href(array('url' => "admin/news",'catedit' => '0'), $this);?>
>Создать новый раздел</a></p>