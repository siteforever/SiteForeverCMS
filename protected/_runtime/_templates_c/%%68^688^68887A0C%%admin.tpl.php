<?php /* Smarty version 2.6.26, created on 2010-10-01 10:33:39
         compiled from system:routes/admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', 'system:routes/admin.tpl', 1, false),array('function', 'icon', 'system:routes/admin.tpl', 4, false),array('function', 'href', 'system:routes/admin.tpl', 5, false),)), $this); ?>
<form method="post" action="<?php echo smarty_function_link(array(), $this);?>
">
    <table class="dataset">
    <tr>
        <th><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</th>
        <th><a <?php echo smarty_function_href(array('recount' => 'yes'), $this);?>
>Порядок</a></th>
        <th>Псевдоним</th>
        <th>Контроллер</th>
        <th>Действие</th>
        <th><?php echo smarty_function_icon(array('name' => 'accept','title' => "Включено"), $this);?>
</th>
        <th><?php echo smarty_function_icon(array('name' => 'lock','title' => "Защищено"), $this);?>
</th>
        <th><?php echo smarty_function_icon(array('name' => 'cog','title' => "Системный"), $this);?>
</th>
    </tr>
    <?php $_from = $this->_tpl_vars['routes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
    <tr>
        <td><input type="checkbox" class="checkbox" name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][delete]" /></td>
        <td><input type="text" class="text"         name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][pos]"           value="<?php echo $this->_tpl_vars['item']['pos']; ?>
" style="width: 50px;" /></td>
        <td><input type="text" class="text"         name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][alias]"         value="<?php echo $this->_tpl_vars['item']['alias']; ?>
" /></td>
        <td><input type="text" class="text"         name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][controller]"    value="<?php echo $this->_tpl_vars['item']['controller']; ?>
" /></td>
        <td><input type="text" class="text"         name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][action]"        value="<?php echo $this->_tpl_vars['item']['action']; ?>
" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][active]"        <?php if ($this->_tpl_vars['item']['active']): ?>checked<?php endif; ?> /></td>
        <td><input type="checkbox" class="checkbox" name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][protected]"     <?php if ($this->_tpl_vars['item']['protected']): ?>checked<?php endif; ?> /></td>
        <td><input type="checkbox" class="checkbox" name="routes[<?php echo $this->_tpl_vars['item']['id']; ?>
][system]"        <?php if ($this->_tpl_vars['item']['system']): ?>checked<?php endif; ?> /></td>
    </tr>
    <?php endforeach; else: ?>
    <tr>
        <td colspan="9"></td>
    </tr>
    <?php endif; unset($_from); ?>
    </table>
    <p>
        <input type="submit" class="submit" value="Сохранить" />
    </p>
</form>

<form method="post" action="<?php echo smarty_function_link(array(), $this);?>
">
    <p>Добавить новый маршрут</p>
    <table class="dataset">
    <tr>
        <td><?php echo smarty_function_icon(array('name' => 'add','title' => "Добавить"), $this);?>
</td>
        <td><input type="text" class="text"         name="routes[0][pos]"         value="" style="width: 50px;" /></td>
        <td><input type="text" class="text"         name="routes[0][alias]"         value="" /></td>
        <td><input type="text" class="text"         name="routes[0][controller]"    value="" /></td>
        <td><input type="text" class="text"         name="routes[0][action]"        value="" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][active]"    checked /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][protected]" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][system]"    /></td>
    </tr>
    </table>

    <p>
        <input type="submit" class="submit" value="Добавить" />
    </p>

</form>