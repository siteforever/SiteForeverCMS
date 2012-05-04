
{if isset( $form )}

    {$form->html()}
    
    <p>
        <a {href url="users/register"}>Регистрация</a>
        | <a {href url="users/restore"}>Забыли пароль?</a>
    </p>

{else}
    
    <ul>
        <li>Вы вошли как: {$user.login}</li>
        {*<li>Email: {$user.email}</li>*}
        {*<li>Дата регистрации: {$user.date|date_format:"%x (%X)"}</li>*}
        {*<li>Последний вход: {$user.last|date_format:"%x (%X)"}</li>*}
        {if $user.perm != $smarty.const.USER_GUEST}
        <li>Ваш статус:
            {if $user.perm == $smarty.const.USER_USER}Покупатель{/if}
            {if $user.perm == $smarty.const.USER_WHOLE}Оптовый покупатель{/if}
            {if $user.perm == $smarty.const.USER_ADMIN}Администратор{/if}
        </li>
        <li><a {href url="order"}>Мои заказы</a></li>
        <li><a {href url="users/edit"}>Редактировать профиль</a></li>
        <li><a {href url="users/password"}>Изменить пароль</a></li>
        {/if}
        {if $user.perm == $smarty.const.USER_ADMIN}
        <li><a {href url="admin"}>Управление сайтом</a></li>
        {/if}
        <li><a {href url="users/logout"}>Выйти из системы</a></li>
    
    </ul>
    
{/if}