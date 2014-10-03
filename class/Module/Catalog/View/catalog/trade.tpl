{form form=$form}
    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('cat')}
    {$form->htmlFieldWrapped('name')}
    {$form->htmlFieldWrapped('title')}
    {$form->htmlFieldWrapped('keywords')}
    {$form->htmlFieldWrapped('description')}
    {$form->htmlFieldWrapped('parent')}
    {$form->htmlFieldWrapped('type_id')}
    {$form->htmlFieldWrapped('articul')}
    {$form->htmlFieldWrapped('price1')}
    {$form->htmlFieldWrapped('price2')}
    {$form->htmlFieldWrapped('gender')}
    {$form->htmlFieldWrapped('manufacturer')}
    {$form->htmlFieldWrapped('material')}
    {$form->htmlFieldWrapped('qty')}
    {$form->htmlFieldWrapped('novelty')}
    {$form->htmlFieldWrapped('sort_view')}
    {$form->htmlFieldWrapped('top')}
    {$form->htmlFieldWrapped('byorder')}
    {$form->htmlFieldWrapped('absent')}

    <h3>Скидка</h3>
    {$form->htmlFieldWrapped('sale')}
    {$form->htmlFieldWrapped('sale_start')}
    {$form->htmlFieldWrapped('sale_stop')}

    <h3>{t cat="catalog"}Properties{/t}</h3>

    <div class="custom-properties">
        {foreach $fields as $field}
        <div class="form-group">
            <label for="field{$field->id}" class="control-label">{$field->name}</label>

            {if $field->type != 'text'}
                {if $field->unit}
                <div class="input-group">
                    <input type="text" id="field{$field->id}" class="form-control"
                           name="field[{$field->id}]" data-type="{$field->type}"
                           value="{$field->getValue($item)}">
                    <div class="input-group-addon">{$field->unit}</div>
                </div>
                {else}
                <input type="text" id="field{$field->id}" class="form-control"
                       name="field[{$field->id}]" data-type="{$field->type}"
                       value="{$field->getValue($item)}">
                {/if}
            {else}
            <textarea id="field{$field->id}" class="form-control plain" name="field[{$field->id}]">
                {$field->getValue($item)}</textarea>
            {/if}
        </div>
        {/foreach}
    </div>

    <div class="category-properties">
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


    {$form->htmlFieldWrapped('hidden')}
    {$form->htmlFieldWrapped('protected')}
    {$form->htmlFieldWrapped('deleted')}



    {$form->htmlField('text')}


    {if isset($gallery_panel)}
    <h3>{t cat="catalog"}Gallery{/t}</h3>
    <div>
    {$gallery_panel}
    </div>
    {/if}
{/form}
