
{$breadcrumbs}

<br />
<label for="goods_filter">Фильтр по артикулу:</label>
<div class="input-append">
    <input type="text" name="goods_filter" id="goods_filter" value="{$filter}"
           title="Введите часть артикула" class="span2" />
    <button id="goods_filter_select" class="btn">Применить</button>
    <button id="goods_filter_cancel" class="btn">Отменить</button>
</div>

<table class="catalog_data dataset fullWidth">
<tr>
    <th colspan="3">Наименование</th>
    <th>Порядок</th>
    <th width="100">Артикул</th>
    <th width="120">Действия</th>
</tr>
{foreach from=$list item="item"}
<tr {if $item.cat}rel="{$item.id}" class="cat"{/if}>
    <td width="20"><input type="checkbox" class="checkbox" name="move[]" value="{$item.id}"></td>
    <td width="30" class="right">{$item.id}</td>
    <td>
        {if $item.cat}
            {icon name="folder" title="Каталог"}
            {a controller="catalog" action="admin" part=$item.id}{$item.name}{/a}
        {else}
            {*icon name="page" title="Товар"*}
            {thumb src=$item.thumb width="50" height="50"}
            {a controller="catalog" action="trade" edit=$item.id class="edit"}{$item.name}{/a}
        {/if}
    </td>
    <td class="trade_pos">{if $item.cat == 0}
            <input class="trade_pos" type="text" rel="{$item.id}" value="{$item.pos|default:"0"}" maxlength="3" />
        {/if}</td>
    <td>{if $item.cat == 1}{$item.child_count}{else}{$item.articul}{/if}</td>
    <td>
        {if $item.cat}
            {a controller="catalog" action="category" edit=$item.id class="edit"}
            {icon name="pencil" title="Править"}{/a}
        {else}
            {a controller="catalog" action="trade" edit=$item.id class="edit"}
            {icon name="pencil" title="Править"}{/a}
        {/if}
        {a controller="catalog" action="hidden" id=$item.id class="order_hidden"}
            {if $item.hidden}{icon name="lightbulb_off" title="Включить"}
            {else}{icon name="lightbulb" title="Выключить"}{/if}{/a}
        {if $item.cat == 1}
        {a controller="catalog" action="category" type="1" add=$item.id}{icon name="folder_add" title="Добавить подраздел"}{/a}
        {a controller="catalog" action="trade" type="0" add=$item.id}{icon name="page_add" title="Добавить товар"}{/a}
        {/if}
        {a controller="catalog" action="delete" id=$item.id class="do_delete"}
            {icon name="delete" title="Удалить"}{/a}
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="6">Пока нет разделов</td>
</tr>
{/foreach}
</table>
<p>
{a controller="catalog" action="trade" add=$parent.id type="0" class="btn edit"}
    {icon name="page_add" title="Добавить товар"} Добавить товар{/a}
{*<select id="catalog_move_target">*}
{*{foreach from=$moving_list item="item" key="key"}<option value="{$key}">{$item}</option>{/foreach}*}
{*</select>*}
{*{a controller="catalog" action="move" part=$parent.id id="catalog_move_to_category" class="btn"}Переместить{/a}*}
{a controller="catalog" action="saveorder" part=$parent.id htmlId="catalog_save_position" class="btn"}
    {icon name="drive" title="Сохранить"} Сохранить порядок товаров{/a}
</p>
<p>{$paging.html}</p>


