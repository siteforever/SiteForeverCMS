<form method="post" action="{link}">
    <table class="dataset">
    <tr>
        <th>{icon name="delete" title="Удалить"}</th>
        <th><a {href recount="yes"}>Порядок</a></th>
        <th>Псевдоним</th>
        <th>Контроллер</th>
        <th>Действие</th>
        <th>{icon name="accept" title="Включено"}</th>
        <th>{icon name="lock" title="Защищено"}</th>
        <th>{icon name="cog" title="Системный"}</th>
    </tr>
    {foreach from=$routes item="item"}
    <tr>
        <td><input type="checkbox" class="checkbox" name="routes[{$item.id}][delete]" /></td>
        <td><input type="text" class="text"         name="routes[{$item.id}][pos]"           value="{$item.pos}" style="width: 50px;" /></td>
        <td><input type="text" class="text"         name="routes[{$item.id}][alias]"         value="{$item.alias}" /></td>
        <td><input type="text" class="text"         name="routes[{$item.id}][controller]"    value="{$item.controller}" /></td>
        <td><input type="text" class="text"         name="routes[{$item.id}][action]"        value="{$item.action}" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[{$item.id}][active]"        {if $item.active}checked{/if} /></td>
        <td><input type="checkbox" class="checkbox" name="routes[{$item.id}][protected]"     {if $item.protected}checked{/if} /></td>
        <td><input type="checkbox" class="checkbox" name="routes[{$item.id}][system]"        {if $item.system}checked{/if} /></td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="9"></td>
    </tr>
    {/foreach}
    </table>
    <p>
        <input type="submit" class="submit" value="Сохранить" />
    </p>
</form>

<form method="post" action="{link}">
    <p>Добавить новый маршрут</p>
    <table class="dataset">
    <tr>
        <td>{icon name="add" title="Добавить"}</td>
        <td><input type="text" class="text"         name="routes[0][pos]"         value="" style="width: 50px;" /></td>
        <td><input type="text" class="text"         name="routes[0][alias]"         value="" /></td>
        <td><input type="text" class="text"         name="routes[0][controller]"    value="" /></td>
        <td><input type="text" class="text"         name="routes[0][action]"        value="" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][active]"    checked /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][protected]" /></td>
        <td><input type="checkbox" class="checkbox" name="routes[0][system]"    /></td>
    </tr>
    </table>

    <p>
        <input type="submit" class="submit" value="Добавить" />
    </p>

</form>