<input type="hidden" id="id" value="{$parent->id}">
<input type="hidden" id="url" value="{link url="page/add"}">

<p><label for="module">{t}Page module{/t}</label></p>
<p><select id="module">
{foreach from=$modules key="name" item="title"}
    <option value="{$name}" {if $name == $parent->controller}selected="selected"{/if}>{$title}</option>
{/foreach}
</select>

</p>
<p><label for="name">{t}Page name{/t}</label></p>
<p><input id="name" type="text"></p>