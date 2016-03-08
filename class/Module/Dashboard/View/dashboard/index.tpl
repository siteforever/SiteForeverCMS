<div class="row">
{foreach $panels as $panel}
{if $panel@index > 0 && ($panel@index % 3) == 0}</div><div class="row">{/if}
<div class="col-md-4">
    <div class="panel panel-default">
        <div class="panel-heading">{$panel.title}</div>
        <div class="panel-body">{$panel.content}</div>
    </div>
</div>
{foreachelse}
<div class="col-sm-4">
    <p>{'Currently there are no panels'|trans}</p>
</div>
{/foreach}
</div>
<div class="clearfix"></div>
