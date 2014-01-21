{form form=$form class="form-horizontal"}
    {$form->htmlFieldWrapped('id')}
    <fieldset>
        <legend>{"System"|lang:"page"}</legend>
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('parent')}
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
        <legend>{"Seo"|lang:"page"}</legend>
        {$form->htmlFieldWrapped('title')}
        {$form->htmlFieldWrapped('keywords')}
        {$form->htmlFieldWrapped('description')}
        {$form->htmlFieldWrapped('nofollow')}
    </fieldset>
    <fieldset>
        <legend>{"Images"|lang:"page"}</legend>
        {$form->htmlFieldWrapped('thumb')}
        {$form->htmlFieldWrapped('image')}
    </fieldset>

    {$form->htmlFieldWrapped('notice')}
    {$form->htmlFieldWrapped('content')}

    <fieldset>
        <legend>{"Constraints"|lang:"page"}</legend>
        {$form->htmlFieldWrapped('author')}
        {$form->htmlFieldWrapped('protected')}
        {$form->htmlFieldWrapped('hidden')}
        {$form->htmlFieldWrapped('system')}
    </fieldset>
{/form}


