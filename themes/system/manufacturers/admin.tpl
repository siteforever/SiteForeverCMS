<table class="dataset">
    <tr>
        <th>{t}Name{/t}</th>
        <th>{t}Email{/t}</th>
        <th>{t}Phone{/t}</th>
        <th></th>
    </tr>
    {foreach from=$rows item="m"}
    <tr>
        <td>{$m.name}</td>
        <td>{$m.email}</td>
        <td>{$m.phone}</td>
        <td class="right">
            <a {href url="manufacturers/edit" id=$m.id} title="{t}Edit manufacturer{/t}">{icon name="pencil" title=t("edit")}</a>
            <a {href url="manufacturers/delete" id=$m.id} title="{t}Want to delete?{/t}" class="delete">{icon name="delete" title=t("delete")}</a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="4">{t}Manufacturers not found{/t}</td>
    </tr>
    {/foreach}
</table>

<p>{$paging->html}</p>

<a {href url="manufacturers/edit"} class="button">{t}Create manufacturer{/t}</a>