<h1>{$request->getTitle()}</h1>

<ul>
    <li>{t cat="user"}You are logged in as:{/t} {$user.login}</li>
    {*<li>Email: {$user.email}</li>*}
    {*<li>Дата регистрации: {$user.date|date_format:"%x (%X)"}</li>*}
    {*<li>Последний вход: {$user.last|date_format:"%x (%X)"}</li>*}
    {if $user.perm != $smarty.const.USER_GUEST}
    <li>{t cat="user"}Your status:{/t}
        {if $user.perm == $smarty.const.USER_USER}{t cat="user"}Buyer{/t}{/if}
        {if $user.perm == $smarty.const.USER_WHOLE}{t cat="user"}Wholesale buyer{/t}{/if}
        {if $user.perm == $smarty.const.USER_ADMIN}{t cat="user"}Administrator{/t}{/if}
    </li>
    <li><a {href url="order"}>{t cat="order"}My orders{/t}</a></li>
    <li><a {href url="user/edit"}>{t cat="user"}Edit profile{/t}</a></li>
    <li><a {href url="user/password"}>{t cat="user"}Change password{/t}</a></li>
    {/if}
    {if $user.perm == $smarty.const.USER_ADMIN}
    <li><a {href url="page/admin"}>{t cat="user"}Site control{/t}</a></li>
    {/if}
    <li><a {href url="user/logout"}>{t cat="user"}Sign out site{/t}</a></li>

</ul>
