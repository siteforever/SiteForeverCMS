{block name="title"}<h2>{$title|trans|ucfirst}</h2>{/block}

{block name="dataset"}
<div class="sfcms-admin-dataset" data-url="{$dataUrl}">
    <div class="pagination" data-pages="{$paging->pages}" data-page="{$paging->page}">
        <ul><li><span>{"pages"|trans|ucfirst}:</span></li></ul>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>{strip}
            {foreach $fields as $field}
                {if empty($field.hidden)}
                <th class="{$field.class|default:""}">{if empty($field.sort)}
                    {$field.label|trans}
                {else}
                    <a href="{$dataUrl}" data-ord="{$field.value}" class="">{$field.label|trans}</a>
                {/if}</th>
                {/if}
            {/foreach}
            <th class="span2">{'Action'|trans}</th>
        {/strip}</tr>
        {if $filtered}
        <tr class="sfcms-admin-dataset-fiter">{strip}
            {foreach $fields as $field}
                {if empty($field.hidden)}
                    {if empty($field.filter)}
                        <th>&mdash;</th>
                    {else}
                        <th><input type="text" class="input-small" data-col="{$field.value}" /></th>
                    {/if}
                {/if}
            {/foreach}
            <th></th>
        {/strip}</tr>
        {/if}
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="pagination" data-pages="{$paging->pages}" data-page="{$paging->page}">
        <ul><li><span>{"pages"|trans|ucfirst}:</span></li></ul>
        <p>&nbsp;</p>
    </div>
    <div class="control-panel">{strip}
        <div class="btn-group">
            <button class="btn btn-refresh"><i class="icon icon-refresh"></i> {"Refresh"|trans}</button>
            <button class="btn btn-add" data-href="{link url="catalogcomment/edit"}">
                <i class="icon icon-plus"></i> {"Create"|trans}</button>
        </div>
    {/strip}</div>
</div>
{/block}


{block name="tplAdminItem"}
<script type="text/x-backbone-template" id="tplAdminItem" class="hide">{strip}
    {foreach $fields as $field}{if empty($field.hidden)}
    <td>
    {if !empty($field.hidden)}
        <span class="icon <% print({$field.value|replace:$dataSeparator:"_"} == 1 ? 'icon-eye-open' : 'icon-eye-open'); %>"></span>
    {elseif !empty($field.bool)}
        <span class="icon <% print({$field.value|replace:$dataSeparator:"_"} == 1 ? 'icon-ok' : 'icon-remove'); %>"></span>
    {else}
        <%- {$field.value|replace:$dataSeparator:"_"} %>
    {/if}
    </td>
    {/if}{/foreach}
    <td>
        <div class="btn-group">
            <a class="btn btn-small edit" title="{"Edit"|trans}" href="#edit" data-id="<%= id %>"><i class="icon icon-edit"></i></a>
            <a class="btn btn-small delete" title="{"Delete"|trans}" href="#delete"><i class="icon icon-trash"></i></a>
        </div>
    </td>
{/strip}</script>

<script type="text/x-backbone-template" id="tplAdminItemEdit" class="hide">{strip}
    <form class="form-horizontal">
    {foreach $fields as $field}{if empty($field.hidden)}
        <div class="control-group">
            {$value = $field.value|replace:$dataSeparator:"_"}
            <label class="control-label" for="{$value}">{$field.value}</label>
            <div class="controls">
                <input type="text" id="{$value}" name="{$value}" value="<%= {$value} %>">
            </div>
        </div>
    {/if}{/foreach}
    </form>
{/strip}</script>
{/block}

{block name="tplAdminPagingItem"}
<script type="text/x-backbone-template" id="tplAdminPagingItem">
    <li class="<%= attrClass %>"><a href="<%= url %>" data-page="<%= number %>"><%- number %></a></li>
</script>
{/block}
