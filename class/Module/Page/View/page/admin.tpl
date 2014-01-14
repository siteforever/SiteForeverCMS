{include file="page/funcorderhidden.tpl"}

{* Print icon *}
{function selectIcon}
{if $branch->controller == "catalog"}{$icon = "folder_table"}{else}
    {if $branch->controller == "gallery"}{$icon = "folder_picture"}{else}
        {if $branch->controller == "news"}{$icon = "folder_feed"}{else}
            {if $branch->controller == "page"}
                {if $branch->link}{$icon = "folder_link"}
                {elseif isset($data[$branch->id])}{$icon = "folder_explore"}
                {else}{$icon = "page"}{/if}
            {else}{$icon = "folder"}{/if}
        {/if}
    {/if}
{/if}
{icon name=$icon}
{/function}


{* Print tree menu *}
{function tree parent=0 level=0}
<ul data-parent="{$parent}" class="tree-container">
    {foreach from=$data[$parent] item="branch"}
    <li data-parent="{$branch->parent}" data-id="{$branch->id}" data-pos="{$branch->pos}" class="tree-node clearfix">
        {selectIcon branch=$branch}
        <span id="item{$branch->id}">
            {a class="edit" title=$this->t('page','Edit page') controller="page" action="edit" edit=$branch->id}{$branch->name}{/a}
            <small>{$branch->alias}</small>
            <span class="tools">
                {*{$branch->linkEdit}*}
                {a class="edit" title=$this->t('page','Edit page') controller="page" action="edit" edit=$branch->id}
                    {icon name="pencil" title=$this->t('page','Edit page')} {t}Edit{/t}{/a}
                {a class="add" id=$branch->id title=$this->t('page','Create page') controller="page" action="create"}
                    {icon name="add" title=$this->t('page','Create page')} {t}Create{/t}{/a}
                {a class="do_delete" title=$this->t('Delete') controller="page" action="delete" id=$branch->id}
                    {icon name="delete" title=$this->t('Delete')} {t}Delete{/t}{/a}
            </span>
            <span class="order">
                {call orderHidden page=$branch}
                <span class="id_number">#{$branch->id}
                    {if $branch->link}&nbsp;{icon name="link"}{$branch->link}{/if}
                </span>
            </span>
        </span>
        {if isset($data[$branch->id])}{tree parent=$branch->id level=$level+1}{/if}
    </li>
    {/foreach}
</ul>
{/function}



<div id="structureWrapper">
    <div class="b-main-structure">{tree data=$data}</div>

    {*<hr />*}

    {*{a htmlClass="realias btn" controller="page" action="realias"}*}
        {*{icon name="arrow_refresh"}*}
        {*{t cat="page"}Conversion of aliases{/t}*}
    {*{/a}*}

    {*{modal id="pageEdit" title=$this->t('Edit page')}*}
    {*{modal id="pageCreate" title=$this->t('Create page')}*}


</div>
