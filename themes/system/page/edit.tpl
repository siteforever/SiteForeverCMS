

<div id="tabs" style="height: 100%">

    <ul>
        <li><a href="#tabs-1">{t cat="page"}Main settings{/t}</a></li>
        <li><a href="#tabs-2">{t cat="page"}Notice{/t}</a></li>
        <li><a href="#tabs-3">{t cat="page"}Content{/t}</a></li>
        <li><a href="#tabs-4">{t cat="page"}Constraints{/t}</a></li>
    </ul>

{form form=$form}

    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('parent')}

    <div id="tabs-1">
        <fieldset>
            <legend>{t cat="page"}System{/t}</legend>
            {$form->htmlFieldWrapped('name')}
            {$form->htmlFieldWrapped('template')}
            {$form->htmlFieldWrapped('alias')}
            {$form->htmlFieldWrapped('date')}
            {$form->htmlFieldWrapped('update')}
            {$form->htmlFieldWrapped('pos')}
            {$form->htmlFieldWrapped('controller')}
            {$form->htmlFieldWrapped('link')}
            {$form->htmlFieldWrapped('action')}
            {$form->htmlFieldWrapped('sort')}
        </fieldset>
        <fieldset>
            <legend>{t cat="page"}Seo{/t}</legend>
            {$form->htmlFieldWrapped('title')}
            {$form->htmlFieldWrapped('keywords')}
            {$form->htmlFieldWrapped('description')}
        </fieldset>
        <fieldset>
            <legend>{t cat="page"}Images{/t}</legend>
            {$form->htmlFieldWrapped('thumb')}
            {$form->htmlFieldWrapped('image')}
        </fieldset>
    </div>

    <div id="tabs-2">
        {$form->htmlField('notice')}
    </div>

    <div id="tabs-3">
        {$form->htmlField('content')}
    </div>

    <div id="tabs-4">
        {$form->htmlFieldWrapped('author')}
        <fieldset>
            <legend>{$form->htmlFieldLabel('hidden')}</legend>
            {$form->htmlField('hidden')}
        </fieldset>
        <fieldset>
            <legend>{$form->htmlFieldLabel('protected')}</legend>
            {$form->htmlField('protected')}
        </fieldset>
        <fieldset>
            <legend>{$form->htmlFieldLabel('system')}</legend>
            {$form->htmlField('system')}
        </fieldset>
    </div>

{/form}

</div>

