<ul class="breadcrumb">
<li>{a controller="news" action="admin"}{t cat="news"}News category{/t}{/a}</li>
<li>{$cat.name} {a controller="news" action="catedit" id=$cat.id class="catEdit" title=$this->t("news","Cat edit")}
    {icon name="pencil" title=$this->t('Edit')}{/a}</li>
<li>{a controller="news" action="edit" cat=$cat.id class="newsEdit" title=$this->t('news','Create article')}
    {t cat="news"}Create article{/t}{/a}</li></ul>

<table class="table table-striped">
<thead>
<tr>
    <th width="20">#</th>
    <th>{t cat="news"}Name{/t}</th>
    <th>{t cat="news"}Date{/t}</th>
    <th>{t cat="news"}Priority{/t}</th>
    <th>{t cat="news"}On main{/t}</th>
    <th>{t cat="news"}Actions{/t}</th>
</tr>
</thead>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        <a {href controller="news" action="edit" id=$item.id} class="newsEdit" title="{t cat="news"}News edit{/t}">
            {$item.name|truncate:100}</a>
    </td>
    <td>{$item.date|date_format:"%Y-%m-%d"}</td>
    <td>{$item.priority}</td>
    <td>{if $item.main}{icon name="accept" title=$this->t('Yes')}{/if}</td>
    <td>
        {if $item.hidden}{icon name="lightbulb_off" title=$this->t('Off')}{else}{icon name="lightbulb" title=$this->t('On')}{/if}
        {if $item.protected}{icon name="lock" title=$this->t('Closed')}{/if}
        <a {href controller="news" action="delete" id=$item.id} class="do_delete">{icon name="delete" title=$this->t('Delete')}</a>
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

{*{modal id="newsEdit" title=$this->t('news','News edit')}*}
