
<form class="ajax" method="post" action="/admin/settings">

    <table class="dataset">
    <tr>
        <th>{icon name="delete" title="Удалить"}</th>
        <th>Параметр</th>
        <th>Значение</th>
        <th>Комментарий</th>
        <th>{icon name="cog" title="Системный"}</th>
    </tr>
    {foreach from=$settings item="item"}
    <tr>
        <td><input type="checkbox" class="checkbox" name="settings[{$item.id}][delete]" /></td>
        <td><input type="text" class="text"         name="settings[{$item.id}][key]"      value="{$item.key}" /></td>
        <td><input type="text" class="text"         name="settings[{$item.id}][value]"    value="{$item.value}" /></td>
        <td><input type="text" class="text"         name="settings[{$item.id}][comment]"  value="{$item.comment}" /></td>
        <td><input type="checkbox" class="checkbox" name="settings[{$item.id}][system]"   {if $item.system}checked{/if} /></td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="5">Настроек не найдено</td>
    </tr>
    {/foreach}
    <tr>
        <td>{icon name="add" title="Добавить"}</td>
        <td><input type="text" class="text"         name="settings[0][key]"     value="" /></td>
        <td><input type="text" class="text"         name="settings[0][value]"   value="" /></td>
        <td><input type="text" class="text"         name="settings[0][comment]" value="" /></td>
        <td><input type="checkbox" class="checkbox" name="settings[0][system]"    /></td>
    </tr>
    </table>

    <p>
        <input type="submit" class="submit" value="Сохранить" />
    </p>

</form>