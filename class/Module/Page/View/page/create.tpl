<div class="form-horizontal">
    <input type="hidden" id="id" value="{$parent->id}">
    <input type="hidden" id="url" value="{link url="page/add"}">

    <div class="control-group">
        <label class="control-label" for="module">{t cat="page"}Page module{/t}</label>
        <div class="controls">
            <select id="module" name="module">
                {foreach from=$modules key="name" item="title"}
                    <option value="{$name}" {if $name == $parent->controller}selected="selected"{/if}>{$title}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="name">{t cat="page"}Page name{/t}</label>
        <div class="controls">
            <input id="name" name="name" type="text">
        </div>
    </div>
</div>