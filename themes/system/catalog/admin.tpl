
{$breadcrumbs}
<br />
<p>
    Фильтр по артикулу: <input name="goods_filter" id="goods_filter" value="{$filter}"
                               title="Введите часть артикула" />
    <button id="goods_filter_select">Применить</button>
    <button id="goods_filter_cancel">Отменить</button>
</p>

<table class="catalog_data dataset fullWidth">
<tr>
    <th colspan="3">Наименование</th>
    <th>Порядок</th>
    <th width="100">Подразделов/Артикул</th>
    <th width="120">Действия</th>
</tr>
{foreach from=$list item="item"}
<tr {if $item.cat}rel="{$item.id}" class="cat"{/if}>
    <td width="20"><input type="checkbox" class="checkbox" name="move[]" value="{$item.id}"></td>
    <td width="30" class="right">{$item.id}</td>
    <td>
        {if $item.cat}
            {icon name="folder" title="Каталог"}
            <a {href url="catalog/admin" part=$item.id}>{$item.name}</a>
        {else}
            {icon name="page" title="Товар"}
            <a {href url="catalog/admin" edit=$item.id}>{$item.name}</a>
        {/if}
    </td>
    <td class="trade_pos">{if $item.cat == 0}
            <input class="trade_pos" type="text" rel="{$item.id}" value="{$item.pos|default:"0"}" maxlength="3" />
        {/if}</td>
    <td>{if $item.cat == 1}{$item.child_count}{else}{$item.articul}{/if}</td>
    <td>
        <a {href url="catalog/admin" edit=$item.id}>{icon name="pencil" title="Править"}</a>
        {if $item.hidden}
            <a {href url="catalog/admin" item=$item.id switch="on"} class="catalog_switch">{icon name="lightbulb_off" title="Включить"}</a>
        {else}
            <a {href url="catalog/admin" item=$item.id switch="off"} class="catalog_switch">{icon name="lightbulb" title="Выключить"}</a>
        {/if}
        {*{if $item.protected}{icon name="lock" title="Снять защиту"}{else}{icon name="lock_add" title="Добавить защиту"}{/if}*}
        {if $item.cat == 1}
        <a {href type="1" add=$item.id}>{icon name="folder_add" title="Добавить подраздел"}</a>
        <a {href type="0" add=$item.id}>{icon name="page_add" title="Добавить товар"}</a>
        {/if}
        <a {href del=$item.id} class="do_delete">{icon name="delete" title="Удалить"}</a></td>
</tr>
{foreachelse}
<tr>
    <td colspan="6">Пока нет разделов</td>
</tr>
{/foreach}
</table>
<p>
<select id="catalog_move_target">
{foreach from=$moving_list item="item" key="key"}<option value="{$key}">{$item}</option>{/foreach}
</select>
<button {href part=$parent.id} id="catalog_move_to_category">Переместить</button>
<button {href part=$parent.id} id="catalog_save_position">Сохранить порядок</button>
</p>
<p>{icon name="folder_add" title="Добавить раздел"} <a {href add=$parent.id type="1"}>Добавить раздел</a> |
{icon name="page_add" title="Добавить товар"} <a {href add=$parent.id type="0"}>Добавить товар</a> |
{icon name="table" title="Прайслист"} <a {href price="load"}>Загрузить прайслист</a></p>
<br />
<p>{$paging.html}</p>