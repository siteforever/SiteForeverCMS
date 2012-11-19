{form form=$form}

{*<ul class="nav nav-tabs">*}
    {*<li class="active"><a href="#main" data-toggle="tab">{t cat="catalog"}Main{/t}</a></li>*}
    {*<li><a href="#properties" data-toggle="tab">{t cat="catalog"}Properties{/t}</a></li>*}
    {*<li><a href="#text" data-toggle="tab">{t cat="catalog"}Text{/t}</a></li>*}
{*{if isset($gallery_panel)}*}
    {*<li><a href="#gallery" data-toggle="tab">{t cat="catalog"}Gallery{/t}</a></li>{/if}*}
    {*<li><a href="#protected" data-toggle="tab">{t cat="catalog"}Protected{/t}</a></li>*}
{*</ul>*}

{*<div class="tab-content">*}
    <fieldset>
    {*<div class="tab-pane active" id="main">*}
        <legend>{t cat="catalog"}Main{/t}</legend>
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
        {$form->htmlFieldWrapped('material')}
        {$form->htmlFieldWrapped('novelty')}
        {$form->htmlFieldWrapped('sort_view')}
        {$form->htmlFieldWrapped('top')}
        {$form->htmlFieldWrapped('byorder')}
        {$form->htmlFieldWrapped('absent')}
    {*</div>*}
    </fieldset>
    <fieldset>
        <legend>{t cat="catalog"}Properties{/t}</legend>
    {*<div class="tab-pane" id="properties">*}
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
    {*</div>*}
    </fieldset>

    <fieldset>{*<div class="tab-pane" id="text">*}
        <legend>{t cat="catalog"}Text{/t}</legend>
        {$form->htmlField('text')}
    {*</div>*}</fieldset>


    {if isset($gallery_panel)}
    <fieldset>{*<div class="tab-pane" id="gallery">*}
        <legend>{t cat="catalog"}Gallery{/t}</legend>
        {$gallery_panel}
    {*</div>*}</fieldset>
    {/if}


    <fieldset>{*<div class="tab-pane" id="protected">*}
        <legend>{t cat="catalog"}Protected{/t}</legend>
        {$form->htmlFieldWrapped('hidden')}
        {$form->htmlFieldWrapped('protected')}
        {$form->htmlFieldWrapped('deleted')}
    {*</div>*}</fieldset>


{*</div>*}

{/form}