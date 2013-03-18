{form form=$form}

<ul class="nav nav-tabs">
    <li class="active"><a href="#tabs-1" data-toggle="tab">{t cat="page"}Main settings{/t}</a></li>
    <li><a href="#tabs-seo" data-toggle="tab">{t cat="page"}SEO{/t}</a></li>
    <li><a href="#tabs-2" data-toggle="tab">{t cat="page"}Notice{/t}</a></li>
    <li><a href="#tabs-3" data-toggle="tab">{t cat="page"}Content{/t}</a></li>
    <li><a href="#tabs-4" data-toggle="tab">{t cat="page"}Constraints{/t}</a></li>
</ul>

    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('author_id')}

<div class="tab-content">
    <div class="tab-pane active" id="tabs-1">
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('cat_id')}
        {$form->htmlFieldWrapped('date')}
        {$form->htmlFieldWrapped('main')}
        {$form->htmlFieldWrapped('priority')}
        {$form->htmlFieldWrapped('image')}
    </div>

    <div class="tab-pane" id="tabs-seo">
        {$form->htmlFieldWrapped('title')}
        {$form->htmlFieldWrapped('keywords')}
        {$form->htmlFieldWrapped('description')}
    </div>

    <div class="tab-pane" id="tabs-2">
        {$form->htmlField('notice')}
    </div>

    <div class="tab-pane" id="tabs-3">
        {$form->htmlField('text')}
    </div>

    <div class="tab-pane" id="tabs-4">
        {$form->htmlFieldWrapped('hidden')}
        {$form->htmlFieldWrapped('protected')}
        {$form->htmlFieldWrapped('deleted')}
    </div>
</div>
{/form}