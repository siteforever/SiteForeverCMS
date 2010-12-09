<?php /* Smarty version 2.6.26, created on 2010-09-27 10:21:38
         compiled from system:users/cabinet.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'href', 'system:users/cabinet.tpl', 7, false),)), $this); ?>

<?php if (isset ( $this->_tpl_vars['form'] )): ?>

    <?php echo $this->_tpl_vars['form']->html(); ?>

    
    <p>
        <a <?php echo smarty_function_href(array('url' => "users/register"), $this);?>
>Регистрация</a>
        | <a <?php echo smarty_function_href(array('url' => "users/restore"), $this);?>
>Забыли пароль?</a>
    </p>

<?php else: ?>
    
    <ul>
        <li>Вы вошли как: <?php echo $this->_tpl_vars['user']['login']; ?>
</li>
                                <?php if ($this->_tpl_vars['user']['perm'] != @USER_GUEST): ?>
        <li>Ваш статус:
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_USER): ?>Покупатель<?php endif; ?>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_WHOLE): ?>Оптовый покупатель<?php endif; ?>
            <?php if ($this->_tpl_vars['user']['perm'] == @USER_ADMIN): ?>Администратор<?php endif; ?>
        </li>
        <li><a <?php echo smarty_function_href(array('url' => 'order'), $this);?>
>Мои заказы</a></li>
        <li><a <?php echo smarty_function_href(array('url' => "users/edit"), $this);?>
>Редактировать профиль</a></li>
        <li><a <?php echo smarty_function_href(array('url' => "users/password"), $this);?>
>Изменить пароль</a></li>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['user']['perm'] == @USER_ADMIN): ?>
        <li><a <?php echo smarty_function_href(array('url' => 'admin'), $this);?>
>Управление сайтом</a></li>
        <?php endif; ?>
        <li><a <?php echo smarty_function_href(array('url' => "users/logout"), $this);?>
>Выйти из системы</a></li>
    
    </ul>
    
    <?php if ($this->_tpl_vars['user']['perm'] != @USER_WHOLE): ?>
        <p>Если Вы представляете организацию и желаете иметь аккаунт оптового 
        покупателя, Вам необходимо заполнить все данные в <a <?php echo smarty_function_href(array('url' => "users/edit"), $this);?>
>профиле</a> 
        и <a <?php echo smarty_function_href(array('url' => "users/option=whole_request"), $this);?>
>отправить нам заявку</a> на изменение статуса аккаунта.</p>
    <?php endif; ?>
    
<?php endif; ?>