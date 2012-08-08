<p><a {href url="news/admin"}>{t cat="news"}News category{/t}</a>
&gt; {$cat.name}
    <a {href controller="news" action="catedit" id=$cat.id} class="catEdit" title="{t cat="news"}Cat edit{/t}">
    {icon name="pencil" title=t('Edit')}</a>
&gt; <a {href controller="news" action="edit" cat=$cat.id} class="newsEdit">{t cat="news"}Create article{/t}</a></p>

<table class="dataset fullWidth">
<tr>
    <th width="20">#</th>
    <th>{t cat="news"}Name{/t}</th>
    <th>{t cat="news"}Date{/t}</th>
    <th>{t cat="news"}Actions{/t}</th>
</tr>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        <a {href controller="news" action="edit" id=$item.id} class="newsEdit" title="{t cat="news"}News edit{/t}")>
            {$item.name|truncate:100}</a>
    </td>
    <td>{$item.date|date_format:"%x"}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title=t('Off')}{else}{icon name="lightbulb" title=t('On')}{/if}
        {if $item.protected}{icon name="lock" title=t('Closed')}{/if}
        <a {href controller="news" action="delete" id=$item.id} class="do_delete">{icon name="delete" title=t('Delete')}</a>
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="4">{t}Nothing was found{/t}</td>
</tr>
{/foreach}
</table>

<p>&nbsp;</p>
{$paging.html}