{form action="admin/users" method="get"}
<p>Фильтр:
    <input type="text" name="search" value="{$request->get('search')}" />
    <input type="submit" value="Фильтровать" />
    {if $request->get('search')}<a {href url="admin/users"} class="button">Сбросить фильтр</a>{/if}
</p>
{/form}

{form action="admin/users" method="post"}
    <table class="dataset fullWidth">
    <tr>
        <th>{icon name="user_delete" title="Удалить"}</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Фамилия</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Зарегистрирован</th>
        <th>Последний вход</th>
    </tr>
    {foreach from=$users item="user"}
    <tr>
        <td><input type="checkbox" class="checkbox" name="users[{$user.id}][delete]" /></td>
        <td>
            {if $user.perm == 0}{icon name="user_gray" title="Гость"}{/if}
            {if $user.perm == 1}{icon name="user_green" title="Пользователь"}{/if}
            {if $user.perm == 2}{icon name="user_orange" title="Опытный"}{/if}
            {if $user.perm == 10}{icon name="user_red" title="Админ"}{/if}
            <a {href url="users/adminEdit" userid=$user.id}">{$user.login}</a>
        </td>
        <td>{$user.email}</td>
        <td>{$user.lname}</td>
        <td>{$user.phone}</td>
        <td>{if $user.status}{icon name="accept" title="Вкл"}{else}{icon name="cross" title="Выкл"}{/if}</td>
        <td>{$user.date|date_format:"%x"}</td>
        <td>{$user.last|date_format:"%x"}</td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="9">Ничего не найдено</td>
    </tr>
    {/foreach}
    </table>
    <p><input type="submit" value="Удалить" /></p>
{/form}

<p>{$paging.html}</p>