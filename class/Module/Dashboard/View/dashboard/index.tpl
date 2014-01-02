{foreach $panels as $panel}
<div class="panel">
    <div class="panel_title">{$panel.title}</div>
    <div class="panel_content">{$panel.content}</div>
</div>
{foreachelse}
<p>{'Currently there are no panels'|trans}</p>
{/foreach}
<div class="clearfix"></div>
