{if $auth}
    {$form->html(false)}
    <p>
        <a {href url="users/register"}>Регистрация</a>
        | <a {href url="users/restore"}>Забыли пароль?</a>
    </p>
{else}
    <p>Вы вошли как <b>{$user.login}</b></p>
    <ul>
        <li>Статус:
            {if $user.perm == $smarty.const.USER_GUEST}Гость{/if}
            {if $user.perm == $smarty.const.USER_USER}Покупатель{/if}
            {if $user.perm == $smarty.const.USER_WHOLE}Оптовый покупатель{/if}
            {if $user.perm == $smarty.const.USER_ADMIN}Администратор{/if}
        </li>
        <li><a {href url="users/cabinet"}>Кабинет пользователя</a></li>
        <li><a {href url="users/logout"}>Выход из системы</a></li>
    </ul>
{/if}