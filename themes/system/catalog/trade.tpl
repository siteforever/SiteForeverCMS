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
        {*<legend>{t cat="catalog"}Main{/t}</legend>*}
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('cat')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('parent')}
        {$form->htmlFieldWrapped('type_id')}
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
        <legend>Скидка</legend>
        {$form->htmlFieldWrapped('sale')}
        {$form->htmlFieldWrapped('sale_start')}
        {$form->htmlFieldWrapped('sale_stop')}
    </fieldset>

    <fieldset>
        <legend>{t cat="catalog"}Properties{/t}</legend>

        <div class="custom-properties">
            {foreach $fields as $field}
            <div class="control-group">
                <label for="field{$field->id}" class="control-label">{$field->name}</label>
                <div class="controls field-text">
                    {if $field->type != 'text'}
                    <input type="text" id="field{$field->id}"
                           name="field[{$field->id}]" data-type="{$field->type}"
                           value="{$field->getValue($item)}">
                    {else}
                    <textarea id="field{$field->id}" class="plain" name="field[{$field->id}]">
                        {$field->getValue($item)}</textarea>
                    {/if}
                    {$field->unit}
                </div>
            </div>
            {/foreach}
        </div>

        <div class="category-properties">
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
        </div>
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