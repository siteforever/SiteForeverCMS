<div style="padding: 0 15px">
    {form form=$form class="form-horizontal"}

        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('author_id')}

        {$form->htmlFieldWrapped('name')}

        {$form->htmlFieldWrapped('alias')}

        {$form->htmlFieldWrapped('cat_id')}

        {$form->htmlFieldWrapped('notice')}

        {$form->htmlFieldWrapped('text')}

        {$form->htmlFieldWrapped('date')}

        {$form->htmlFieldWrapped('image')}

        <h3>SEO</h3>
        {$form->htmlFieldWrapped('title')}
        {$form->htmlFieldWrapped('keywords')}
        {$form->htmlFieldWrapped('description')}

        <h3>{'Settings'|trans}</h3>
        {$form->htmlFieldWrapped('main')}
        {$form->htmlFieldWrapped('hidden')}

        {$form->htmlFieldWrapped('priority')}
        {$form->htmlFieldWrapped('protected')}
        {$form->htmlFieldWrapped('deleted')}
    {/form}
</div>
