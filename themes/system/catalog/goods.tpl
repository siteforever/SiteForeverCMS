<table class="dataset">
    <tr>
        <th>ID</th>
        <th>{t}Name{/t}</th>
        <th>{t}Category{/t}</th>
        <th>{t}Manufacturer{/t}</th>
        <th>&nbsp;</th>
        <th>{t}Articul{/t}</th>
        <th>{t}Price{/t}</th>
        <th></th>
    </tr>
    {foreach from=$list item="item"}
    <tr>
        <td>{$item->id}</td>
        <td>
            {thumb src=$item.image width="50" height="50"}
            {a url="catalog/trade" edit=$item->id class="edit"}{$item->name}{/a}
        </td>
        <td>{if $item->Category}
                {a controller="catalog" action="admin" part=$item->parent}{$item->Category->name}{/a}
            {else}&mdash;{/if}</td>
        <td>{if $item->Manufacturer}{$item->Manufacturer->name}{else}&mdash;{/if}</td>
        <td>{if $item->novelty}{icon name="new"}{else}&nbsp;{/if}</td>
        <td>{$item->articul}</td>
        <td>{$item->price1} ({$item->price2})</td>
        <td>
            {a href="catalog/trade" edit=$item->id class="edit"}{icon name="pencil" title=t("Edit")}{/a}
            {a href="catalog/delete" id=$item->id class="do_delete" title=t('Want to delete?')}
                {icon name="delete" title=t("Delete")}{/a}
        </td>
    </tr>
    {/foreach}
</table>

<p>{$pager->html}</p>