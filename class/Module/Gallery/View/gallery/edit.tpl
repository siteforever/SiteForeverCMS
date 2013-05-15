{$form->htmlStart()}

<ul class="nav nav-tabs">
    <li class="active"><a href="#tabs-basic"  data-toggle="tab">{t}Basic{/t}</a></li>
    <li><a href="#tabs-description"  data-toggle="tab">{t}Description{/t}</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="tabs-basic">
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('link')}
    </div>
    <div class="tab-pane" id="tabs-description">
        {$form->htmlField('description')}
    </div>
</div>
{$form->htmlEnd()}