<form action="{link url="admin/users"}" method="post">
<p>Фильтр:
    <input type="text" name="search" value="{$smarty.post.search|default:""}"  />
    <input type="submit" value="Найти" />
    {if ! empty( $smarty.post.search )}<a {href url="admin/users"}>Сбросить фильтр</a>{/if}
</p>
</form>


<form method="post" action="/admin/users">
    <table class="dataset fullWidth">
    <tr>
        <th>{icon name="delete" title="Удалить"}</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Фамилия</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Зарегистрирован</th>
        <th>Последний вход</th>
        <th>Группа</th>
    </tr>
    {foreach from=$users item="user"}
    <tr>
        <td><input type="checkbox" class="checkbox" name="users[{$user.id}][delete]" /></td>
        <td><a {href url="admin/users" userid=$user.id}">{$user.login}</a></td>
        <td>{$user.email}</td>
        <td>{$user.lname}</td>
        <td>{$user.phone}</td>
        <td>{if $user.status}{icon name="accept" title="Вкл"}{else}{icon name="cross" title="Выкл"}{/if}</td>
        <td>{$user.date|date_format:"%x"}</td>
        <td>{$user.last|date_format:"%x"}</td>
        <td>
            {if $user.perm == $smarty.const.USER_GUEST}{icon name="user_gray" title=$groups[$smarty.const.USER_GUEST]}{/if}
            {if $user.perm == $smarty.const.USER_USER}{icon name="user_green" title=$groups[$smarty.const.USER_USER]}{/if}
            {if $user.perm == $smarty.const.USER_WHOLE}{icon name="user_orange" title=$groups[$smarty.const.USER_WHOLE]}{/if}
            {if $user.perm == $smarty.const.USER_ADMIN}{icon name="user_red" title=$groups[$smarty.const.USER_ADMIN]}{/if}
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="9">Ничего не найдено</td>
    </tr>
    {/foreach}
    </table>
    <p><input type="submit" value="Удалить" /></p>
</form>

<p>{$paging.html}</p>