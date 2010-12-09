<?php /* Smarty version 2.6.26, created on 2010-09-24 17:36:37
         compiled from system:order/admin.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'link', 'system:order/admin.tpl', 1, false),array('function', 'href', 'system:order/admin.tpl', 27, false),array('modifier', 'date_format', 'system:order/admin.tpl', 46, false),)), $this); ?>
<form action="<?php echo smarty_function_link(array('url' => "admin/order"), $this);?>
" method="post">
<p><strong>Настройка фильтра</strong></p>
<table style="border-collapse:separate; border-spacing: 2px;">
<tr>
    <td>Номер</td>
    <td><input name="number" value="<?php echo $_POST['number']; ?>
" /></td>
    <td></td>
</tr>
<tr>
    <td>Дата</td>
    <td><input name="date" value="<?php echo $_POST['date']; ?>
" class="datepicker" /></td>
    <td></td>
</tr>
<tr>
    <td>Аккаунт</td>
    <td><input name="user" value="<?php echo $_POST['user']; ?>
" /></td>
    <td></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit" value="Фильтровать" /></td>
    <td><a <?php echo smarty_function_href(array('url' => "admin/order"), $this);?>
>Сбросить фильтр</a></td>
</tr>
</table>
</form>

<p></p>

<table class="dataset fullWidth">
<tr>
    <th>№</th>
    <th>Аккаунт</th>
    <th>Статус</th>
    <th>Строк</th>
    <th>Позиций</th>
    <th>Сумма</th>
</tr>
<?php $_from = $this->_tpl_vars['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['order']):
?>
<tr>
    <td><a <?php echo smarty_function_href(array('url' => "admin/order",'num' => $this->_tpl_vars['order']['id']), $this);?>
>Заказ №<?php echo $this->_tpl_vars['order']['id']; ?>
</a>
        <small>от <?php echo ((is_array($_tmp=$this->_tpl_vars['order']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%x") : smarty_modifier_date_format($_tmp, "%x")); ?>
</small></td>
    <td><?php echo $this->_tpl_vars['order']['email']; ?>
</td>
    <td><?php echo $this->_tpl_vars['order']['status_value']; ?>
</td>
    <td><?php echo $this->_tpl_vars['order']['pos_num']; ?>
</td>
    <td><?php echo $this->_tpl_vars['order']['count']; ?>
</td>
    <td><?php echo $this->_tpl_vars['order']['summa']; ?>
</td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

<p><?php echo $this->_tpl_vars['paging']['html']; ?>
</p>