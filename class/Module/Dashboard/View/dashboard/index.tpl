{foreach $panels as $panel}
<div class="panel">
    <div class="panel_title">{$panel.title}</div>
    <div class="panel_content">{$panel.content}</div>
</div>
{foreachelse}
<p>Пока нет панелей</p>
{/foreach}
<div class="clearfix"></div>
