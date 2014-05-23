<div class="form-horizontal">
    <input type="hidden" id="id" value="{$parent->id}">
    <input type="hidden" id="url" value="{link url="page/add"}">

    <div class="form-group">
        <label class="col-sm-4 control-label" for="module">{t cat="page"}Page module{/t}</label>
        <div class="col-sm-8">
            <select id="module" name="module" class="form-control">
                {foreach from=$modules key="name" item="title"}
                    <option value="{$name}" {if $name == $parent->controller}selected="selected"{/if}>{$title}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label" for="name">{t cat="page"}Page name{/t}</label>
        <div class="col-sm-8">
            <input id="name" name="name" type="text" class="form-control">
        </div>
    </div>
</div>
