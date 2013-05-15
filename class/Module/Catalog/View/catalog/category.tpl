{form form=$form}

{*<ul class="nav nav-tabs">*}
    {*<li class="active"><a href="#main" data-toggle="tab">{t cat="catalog"}Main{/t}</a></li>*}
    {*<li><a href="#properties" data-toggle="tab">{t cat="catalog"}Properties{/t}</a></li>*}
    {*<li><a href="#text" data-toggle="tab">{t cat="catalog"}Text{/t}</a></li>*}
{*{if isset($gallery_panel)}*}
    {*<li><a href="#gallery" data-toggle="tab">{t cat="catalog"}Gallery{/t}</a></li>{/if}*}
    {*<li><a href="#protected" data-toggle="tab">{t cat="catalog"}Protected{/t}</a></li>*}
{*</ul>*}

    <fieldset class="tab-pane active" id="main">
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('cat')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('parent')}
        {*{$form->htmlFieldWrapped('path')}*}
        {*{$form->htmlFieldWrapped('articul')}*}
        {*{$form->htmlFieldWrapped('price1')}*}
        {*{$form->htmlFieldWrapped('price2')}*}
        {*{$form->htmlFieldWrapped('manufacturer')}*}
    </fieldset>
    <fieldset class="tab-pane" id="properties">
        <legend>{t cat="catalog"}Properties{/t}</legend>
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
    </fieldset>
    {*<fieldset class="tab-pane" id="text">*}
        {*<legend>{t cat="catalog"}Text{/t}</legend>*}
        {*{$form->htmlField('text')}*}
    {*</fieldset>*}
    {*{if isset($gallery_panel)}*}
    {*<fieldset class="tab-pane" id="gallery">*}
        {*<legend>{t cat="catalog"}Gallery{/t}</legend>*}
        {*{$gallery_panel}*}
    {*</fieldset>{/if}*}
    {*<fieldset class="tab-pane" id="protected">*}
        {*<legend>{t cat="catalog"}Protected{/t}</legend>*}
        {*{$form->htmlFieldWrapped('sort_view')}*}
        {*{$form->htmlFieldWrapped('top')}*}
        {*{$form->htmlFieldWrapped('byorder')}*}
        {*{$form->htmlFieldWrapped('absent')}*}
        {*{$form->htmlFieldWrapped('hidden')}*}
        {*{$form->htmlFieldWrapped('protected')}*}
    {*</fieldset>*}

{/form}