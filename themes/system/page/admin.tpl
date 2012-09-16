{include file="system:page/funcorderhidden.tpl"}

{* Print icon *}
{function selectIcon}
{if $branch->controller == "catalog"}{$icon = "folder_table"}{else}
    {if $branch->controller == "gallery"}{$icon = "folder_picture"}{else}
        {if $branch->controller == "news"}{$icon = "folder_feed"}{else}
            {if $branch->controller == "page"}
                {if isset($data[$branch->id])}{$icon = "folder_explore"}{else}{$icon = "page"}{/if}
            {else}{$icon = "folder"}{/if}
        {/if}
    {/if}
{/if}
{icon name=$icon}
{/function}


{* Print tree menu *}
{function tree parent=0 level=0}
<ul data-parent="{$parent}">
    {foreach from=$data[$parent] item="branch"}
    <li data-parent="{$branch->parent}" data-id="{$branch->id}" data-pos="{$branch->pos}">
        <span id="item{$branch->id}">
            {selectIcon branch=$branch}
            {a class="edit" title=t('page','Edit page') controller="page" action="edit" edit=$branch->id}{$branch->name}{/a}
            <span class="tools">
                {*{$branch->linkEdit}*}
                <small>
                    {a class="edit" title=t('page','Edit page') controller="page" action="edit" edit=$branch->id}
                        {icon name="pencil" title=t('page','Edit page')} {t}Edit{/t}{/a}
                    {a class="add" id=$branch->id title=t('page','Create page') controller="page" action="create"}
                        {icon name="add" title=t('page','Create page')} {t}Create{/t}{/a}
                    {a class="do_delete" title=t('Delete') controller="page" action="delete" id=$branch->id}
                        {icon name="delete" title=t('Delete')} {t}Delete{/t}{/a}
                </small>
            </span>
            <span class="order">
                {call orderHidden page=$branch}
                <span class="id_number">#{$branch->id}</span>
            </span>
        </span>
        {if isset($data[$branch->id])}{tree parent=$branch->id level=$level+1}{/if}
    </li>
    {/foreach}
</ul>
{/function}



<div id="structureWrapper">
    <div class="b-main-structure">{tree data=$data}</div>

    <hr />

    {a htmlClass="realias btn" controller="page" action="realias"}
        {icon name="arrow_refresh"}
        {t cat="page"}Conversion of aliases{/t}
    {/a}


    {*{modal id="pageEdit" title=t('Edit page')}*}
    {*{modal id="pageCreate" title=t('Create page')}*}


</div>