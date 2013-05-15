{form class="well form-horizontal" action="user/admin" method="get"}
    <div class="input-append">
        <input type="text" name="search" id="search" class="input-xlarge" value="{$request->get('search')}" />
        <input type="submit" class="btn" value="Фильтровать" />
    {if $request->get('search')}{a controller="user" action="admin" class="btn"}Сбросить фильтр{/a}{/if}
    </div>
{/form}

{form action="user/admin" method="post"}
    <table class="table table-striped">
    <thead>
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
    </thead>
    <tbody>
        {foreach from=$users item="user"}
        <tr>
            <td><input type="checkbox" class="checkbox" name="users[{$user.id}][delete]" /></td>
            <td>
                {if $user.perm == 0}{icon name="user_gray" title="Гость"}{/if}
                {if $user.perm == 1}{icon name="user_green" title="Пользователь"}{/if}
                {if $user.perm == 2}{icon name="user_orange" title="Опытный"}{/if}
                {if $user.perm == 10}{icon name="user_red" title="Админ"}{/if}
                {a controller="user" action="adminEdit" id=$user.id class="edit" title=t('Edit')}{$user.login}{/a}
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
    </tbody>
    </table>
    <p><input type="submit" class="btn" value={t}Delete{/t} /></p>
{/form}

<p>{$paging.html}</p>
