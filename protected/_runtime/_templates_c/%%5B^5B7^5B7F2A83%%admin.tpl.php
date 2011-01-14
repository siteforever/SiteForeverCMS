<?php /* Smarty version 2.6.26, created on 2011-01-15 01:55:42
         compiled from system:users/admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', 'system:users/admin.tpl', 1, false),array('function', 'href', 'system:users/admin.tpl', 5, false),array('function', 'icon', 'system:users/admin.tpl', 13, false),array('modifier', 'date_format', 'system:users/admin.tpl', 31, false),)), $this); ?>
<form action="<?php echo smarty_function_link(array('url' => "admin/users"), $this);?>
" method="post">
<p>Фильтр:
    <input type="text" name="search" value="<?php echo $_POST['search']; ?>
"  />
    <input type="submit" value="Найти" />
    <?php if ($_POST['search'] != ''): ?><a <?php echo smarty_function_href(array('url' => "admin/users"), $this);?>
>Сбросить фильтр</a><?php endif; ?>
</p>
</form>


<form method="post" action="/admin/users">
    <table class="dataset fullWidth">
    <tr>
        <th><?php echo smarty_function_icon(array('name' => 'delete','title' => "Удалить"), $this);?>
</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Фамилия</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Зарегистрирован</th>
        <th>Последний вход</th>
        <th>Группа</th>
    </tr>
    <?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
    <tr>
        <td><input type="checkbox" class="checkbox" name="users[<?php echo $this->_tpl_vars['user']['id']; ?>
][delete]" /></td>
        <td><a <?php echo smarty_function_href(array('url' => "admin/users",'userid' => $this->_tpl_vars['user']['id']), $this);?>
"><?php echo $this->_tpl_vars['user']['login']; ?>
</a></td>
        <td><?php echo $this->_tpl_vars['user']['email']; ?>
</td>
        <td><?php echo $this->_tpl_vars['user']['lname']; ?>
</td>
        <td><?php echo $this->_tpl_vars['user']['phone']; ?>
</td>
        <td><?php if ($this->_tpl_vars['user']['status']): ?><?php echo smarty_function_icon(array('name' => 'accept','title' => "Вкл"), $this);?>
<?php else: ?><?php echo smarty_function_icon(array('name' => 'cross','title' => "Выкл"), $this);?>
<?php endif; ?></td>
        <td><?php echo ((is_array($_tmp=$this->_tpl_vars['user']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</td>
        <td><?php echo ((is_array($_tmp=$this->_tpl_vars['user']['last'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</td>
        <td>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_GUEST): ?><?php echo smarty_function_icon(array('name' => 'user_gray','title' => $this->_tpl_vars['groups'][@USER_GUEST]), $this);?>
<?php endif; ?>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_USER): ?><?php echo smarty_function_icon(array('name' => 'user_green','title' => $this->_tpl_vars['groups'][@USER_USER]), $this);?>
<?php endif; ?>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_WHOLE): ?><?php echo smarty_function_icon(array('name' => 'user_orange','title' => $this->_tpl_vars['groups'][@USER_WHOLE]), $this);?>
<?php endif; ?>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_ADMIN): ?><?php echo smarty_function_icon(array('name' => 'user_red','title' => $this->_tpl_vars['groups'][@USER_ADMIN]), $this);?>
<?php endif; ?>
        </td>
    </tr>
    <?php endforeach; else: ?>
    <tr>
        <td colspan="9">Ничего не найдено</td>
    </tr>
    <?php endif; unset($_from); ?>
    </table>
    <p><input type="submit" value="Удалить" /></p>
</form>

<p><?php echo $this->_tpl_vars['paging']['html']; ?>
</p>