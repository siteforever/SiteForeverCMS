<h2>{t}Model Generator{/t}</h2>

<h3>{t}Select table for model generation{/t}</h3>
<ul>
    {foreach from=$tables item="t"}
    <li><a href="#{$t}" class="sfcms_generation_table">{$t}</a></li>
    {/foreach}
</ul>