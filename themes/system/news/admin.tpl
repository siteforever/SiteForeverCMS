<table class="table table-striped">
<thead>
<tr>
    <th width="20">#</th>
    <th>{t cat="news"}Name{/t}</th>
    <th>{t cat="news"}Description{/t}</th>
    <th>{t cat="news"}Articles{/t}</th>
    <th>{t cat="news"}Action{/t}</th>
</tr>
</thead>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        <div class="row-fluid">
            <div class="span6"><big><a {href controller="news" action="list" id=$item.id}>{$item.name}</a></big></div>
            <div class="span6">
                <small>
                    {if $item.hidden}
                        {icon name="lightbulb_off" title=t('Off')} {t}Off{/t}
                    {else}
                        {icon name="lightbulb" title=t('On')} {t}On{/t}
                    {/if}
                    {if $item.protected}
                        {icon name="lock" title=t('Closed')} {t}Closed{/t}
                    {/if}
                    {a controller="news" action="catedit" id=$item.id class="catEdit"}
                        {icon name="pencil" title=t("Edit")} {t}Edit{/t}{/a}
                    {a controller="news" action="catdelete" id=$item.id class="do_delete"}
                        {icon name="delete" title=t("Delete")} {t}Delete{/t}{/a}
                </small>
            </div>
        </div>

    </td>
    <td>{$item.description}</td>
    <td>{$item.news_count}</td>
    <td>
    </td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">{t}Nothing was found{/t}</td>
</tr>
{/foreach}
</table>
<p></p>

{*{modal id="newsCatEdit" title=t('news','News category edit')}*}
{*<p><a class="button" {href controller="news" action="catedit" id="0"}>*}
    {*{icon name="add"} Создать новый раздел</a></p>*}