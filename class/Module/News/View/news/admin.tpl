<p>
    {a controller="news" action="edit" class="btn btn-success newsEdit" title=$this->t('news','Create article')}
        <i class="glyphicon glyphicon-plus"></i>
        {t cat="news"}Create article{/t}
    {/a}
    <button class="btn btn-warning btn-edit">
        <i class="glyphicon glyphicon-pencil"></i>
        Редактировать выделенную статью</button>
    {a controller="news" action="delete" class="btn btn-danger btn-delete"}
        <i class="glyphicon glyphicon-delete"></i>
        Удалить выделенную статью
    {/a}
</p>

<table class="table table-striped" id="news_grid" data-url="/news/list"></table>

{modal id="newsEdit" title=$this->t('news','News edit')}

<script>
window.new_categories = {
    "0": "Все"
};
{foreach from=$list item="item"}
window.new_categories["{$item.id}"] = "{$item.name}";
{/foreach}
</script>

{*<table class="table table-striped">
<thead>
<tr>
    <th width="20">#</th>
    <th>{t cat="news"}Name{/t}</th>
    <th>{t cat="news"}Description{/t}</th>
</tr>
</thead>
{foreach from=$list item="item"}
<tr>
    <td>{$item.id}</td>
    <td>
        {if $item.hidden}
            {icon name="lightbulb_off" title=$this->t('Off')}
        {else}
            {icon name="lightbulb" title=$this->t('On')}
        {/if}
        {if $item.protected}
            {icon name="lock" title=$this->t('Closed')}
        {/if}
        {a controller="news" action="catedit" id=$item.id class="catEdit" title=$this->t("news","Cat edit")}
        {icon name="pencil" title=$this->t("Edit")}{/a}
        {a href="news/list" id=$item.id}{$item.name}{/a}
    </td>
    <td>{$item.description}</td>
</tr>
{foreachelse}
<tr>
    <td colspan="5">{t}Nothing was found{/t}</td>
</tr>
{/foreach}
</table>
<p></p>*}

{*{modal id="newsCatEdit" title=$this->t('news','News category edit')}*}
{*<p><a class="button" {href controller="news" action="catedit" id="0"}>*}
    {*{icon name="add"} Создать новый раздел</a></p>*}
