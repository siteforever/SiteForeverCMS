<table class="dataset">
    <tr>
        <th>ID</th>
        <th>{t}Name{/t}</th>
        <th>{t}Category{/t}</th>
        <th>{t}Manufacturer{/t}</th>
        <th>{t}Articul{/t}</th>
        <th>{t}Price{/t}</th>
        <th></th>
    </tr>
    {foreach from=$list item="item"}
    <tr>
        <td>{$item->id}</td>
        <td>
            <a {href url="catalog/trade" edit=$item->id} class="edit">{$item->name}</a>
        </td>
        <td>{if $item->Category}{$item->Category->name}{else}&mdash;{/if}</td>
        <td>{if $item->Manufacturer}{$item->Manufacturer->name}{else}&mdash;{/if}</td>
        <td>{$item->articul}</td>
        <td>{$item->price1} ({$item->price2})</td>
        <td>
            <a {href url="catalog/trade" edit=$item->id} class="edit">{icon name="pencil" title=t("Edit")}</a>
            <a {href url="catalog/delete" id=$item->id} class="delete" title=t('Want to delete?')>
                {icon name="delete" title=t("Delete")}</a>
        </td>
    </tr>
    {/foreach}
</table>

<p>{$pager->html}</p>