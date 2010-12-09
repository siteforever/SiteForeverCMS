<?php /* Smarty version 2.6.26, created on 2010-09-12 02:40:43
         compiled from system:users/profile.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:users/profile.tpl', 7, false),)), $this); ?>
<h2>Изменить профиль</h2>

<?php echo $this->_tpl_vars['form']->html(); ?>


<p>** - необходимо заполнить для организаций</p>
<ul>
    <li><a <?php echo smarty_function_href(array('url' => "users/cabinet"), $this);?>
>Вернуться в кабинет</a></li>
</ul>