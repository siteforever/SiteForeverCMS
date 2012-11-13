{form form=$form}

<ul class="nav nav-tabs">
    <li class="active"><a href="#main" data-toggle="tab">{t cat="catalog"}Main{/t}</a></li>
    <li><a href="#properties" data-toggle="tab">{t cat="catalog"}Properties{/t}</a></li>
    <li><a href="#text" data-toggle="tab">{t cat="catalog"}Text{/t}</a></li>
{if isset($gallery_panel)}
    <li><a href="#gallery" data-toggle="tab">{t cat="catalog"}Gallery{/t}</a></li>{/if}
    <li><a href="#protected" data-toggle="tab">{t cat="catalog"}Protected{/t}</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="main">
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('cat')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('parent')}
        {*{$form->htmlFieldWrapped('path')}*}
        {$form->htmlFieldWrapped('articul')}
        {$form->htmlFieldWrapped('price1')}
        {$form->htmlFieldWrapped('price2')}
        {$form->htmlFieldWrapped('gender')}
        {$form->htmlFieldWrapped('manufacturer')}
        {$form->htmlFieldWrapped('novelty')}
        {$form->htmlFieldWrapped('sort_view')}
        {$form->htmlFieldWrapped('top')}
        {$form->htmlFieldWrapped('byorder')}
        {$form->htmlFieldWrapped('absent')}
    </div>
    <div class="tab-pane" id="properties">
        {$form->htmlFieldWrapped('p0')}
        {$form->htmlFieldWrapped('p1')}
        {$form->htmlFieldWrapped('p2')}
        {$form->htmlFieldWrapped('p3')}
        {$form->htmlFieldWrapped('p4')}
        {$form->htmlFieldWrapped('p5')}
        {$form->htmlFieldWrapped('p6')}
        {$form->htmlFieldWrapped('p7')}
        {$form->htmlFieldWrapped('p8')}
        {$form->htmlFieldWrapped('p9')}
    </div>
    <div class="tab-pane" id="text">
        {$form->htmlField('text')}
    </div>
    {if isset($gallery_panel)}
    <div class="tab-pane" id="gallery">
        {$gallery_panel}
    </div>{/if}
    <div class="tab-pane" id="protected">
        {$form->htmlFieldWrapped('hidden')}
        {$form->htmlFieldWrapped('protected')}
        {$form->htmlFieldWrapped('deleted')}
    </div>
</div>

{/form}