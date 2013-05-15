<h2>{t}Model Generator{/t}</h2>

<h3>{t}Select table for model generation{/t}</h3>
<table class="dataset">
    {foreach from=$tables item="t"}
    <tr><td><a href="#{$t}" class="sfcms_generation_table">{$t}</a></td></tr>
    {/foreach}
</table>