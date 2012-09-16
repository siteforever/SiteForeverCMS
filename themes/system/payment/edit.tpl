{form form=$form}
{$form->htmlFieldWrapped('id')}
<ul class="nav nav-tabs">
<li class="active"><a href="#main" data-toggle="tab">{t}Main{/t}</a></li>
<li><a href="#desc" data-toggle="tab">{t}Desc{/t}</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="main">
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('module')}
        {$form->htmlFieldWrapped('active')}
    </div>
    <div class="tab-pane" id="desc">
        {$form->htmlField('desc')}
    </div>
</div>
{/form}
