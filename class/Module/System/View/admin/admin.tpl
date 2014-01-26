{block name="title"}<h2>{$title|trans}</h2>{/block}

{block name="dataset"}
<div class="sfcms-admin-dataset" data-url="{$dataUrl}">
    <table class="table table-striped">
        <thead>
        <tr>
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
        </tr>
        {if $filtered}
        <tr class="sfcms-admin-dataset-fiter">
            {foreach $fields as $field}
                {if empty($field.hidden)}
                    {if empty($field.filter)}
                        <th></th>
                    {else}
                        <th><input type="text" class="input-small" data-col="{$field.value}" /></th>
                    {/if}
                {/if}
            {/foreach}
            <th></th>
        </tr>
        {/if}
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="row-fluid">
        <div class="control-panel span3">
            <button class="btn btn-small btn-refresh"><i class="icon icon-refresh"></i> {"Refresh"|trans}</button>
            <button class="btn btn-small btn-add" data-href="{link url="catalogcomment/edit"}">
                <i class="icon icon-plus"></i> {"Create"|trans}</button>
        </div>
        <div class="span9">
            <div class="pagination pagination-mini" data-pages="{$paging->pages}" data-page="{$paging->page}">
                <ul><li class="disabled"><span>{"Pages"|trans}:</span></li></ul>
            </div>
        </div>
    </div>
</div>
{/block}




{block name="tplAdminItem"}
<script type="text/x-backbone-template" id="tplAdminItem">
    {foreach $fields as $field}{if empty($field.hidden)}
    <td>
    {if !empty($field.bool)}
        <span class="icon <% print({$field.value|replace:$dataSeparator:"_"} == 1 ? 'icon-ok' : 'icon-remove'); %>"></span>
    {elseif !empty($field.hidden)}
        <span class="icon <% print({$field.value|replace:$dataSeparator:"_"} == 1 ? 'icon-eye-open' : 'icon-eye-open'); %>"></span>
    {else}
        <%- {$field.value|replace:$dataSeparator:"_"} %>
    {/if}
    </td>
    {/if}{/foreach}
    <td>
        <a class="btn btn-small edit" title="{"Edit"|trans}" href="#edit" data-id="<%= id %>"><i class="icon icon-edit"></i></a>
        <a class="btn btn-small delete" title="{"Delete"|trans}" href="#delete"><i class="icon icon-trash"></i></a>
    </td>
</script>
<script type="text/x-backbone-template" id="tplAdminItemEdit">
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
</script>
{/block}




{block name="tplAdminPagingItem"}
    <script type="text/x-backbone-template" id="tplAdminPagingItem">
        <li class="<%= attrClass %>"><a href="<%= url %>" data-page="<%= number %>"><%- number %></a></li>
    </script>
{/block}
