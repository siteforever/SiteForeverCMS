<table class="dataset fullWidth">
<tr>
    <th width="20">#</th>
    <th>{t cat="news"}Name{/t}</th>
    <th>{t cat="news"}Description{/t}</th>
    <th>{t cat="news"}Articles{/t}</th>
    <th>{t cat="news"}Parameters{/t}</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        <a {href controller="news" action="list" id=$item.id}>{$item.name}</a>
        <a {href controller="news" action="catedit" id=$item.id} class="catEdit">{icon name="pencil" title=t("Edit")}</a>
        <a {href controller="news" action="catdelete" id=$item.id} class="do_delete">{icon name="delete" title=t("Delete")}</a>
    </td>
    <td>{$item.description}</td>
    <td>{$item.news_count}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title=t('Off')}{else}{icon name="lightbulb" title=t('On')}{/if}
        {if $item.protected}{icon name="lock" title=t('Close')}{/if}
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">{t}Nothing was found{/t}</td>
</tr>
{/foreach}
</table>
<p></p>
{*<p><a class="button" {href controller="news" action="catedit" id="0"}>*}
    {*{icon name="add"} Создать новый раздел</a></p>*}