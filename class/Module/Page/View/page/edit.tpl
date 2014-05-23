{form form=$form}
    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('name')}
    {$form->htmlFieldWrapped('parent')}
    {$form->htmlFieldWrapped('template')}
    {$form->htmlFieldWrapped('alias')}
    {$form->htmlFieldWrapped('notice')}
    {$form->htmlFieldWrapped('content')}
    <h3>{"System"|lang:"page"}</h3>
    {$form->htmlFieldWrapped('date')}
    {$form->htmlFieldWrapped('update')}
    {$form->htmlFieldWrapped('pos')}
    {$form->htmlFieldWrapped('controller')}
    {$form->htmlFieldWrapped('link')}
    {$form->htmlFieldWrapped('action')}
    {$form->htmlFieldWrapped('sort')}
    <h3>{"Seo"|lang:"page"}</h3>
    {$form->htmlFieldWrapped('title')}
    {$form->htmlFieldWrapped('keywords')}
    {$form->htmlFieldWrapped('description')}
    {$form->htmlFieldWrapped('nofollow')}
    <h3>{"Images"|lang:"page"}</h3>
    {$form->htmlFieldWrapped('thumb')}
    {$form->htmlFieldWrapped('image')}
    <h3>{"Constraints"|lang:"page"}</h3>
    {$form->htmlFieldWrapped('author')}
    {$form->htmlFieldWrapped('protected')}
    {$form->htmlFieldWrapped('hidden')}
    {$form->htmlFieldWrapped('system')}
{/form}


