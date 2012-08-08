<div id="tabs" style="height: 100%">

    <ul>
        <li><a href="#tabs-basic">{t}Basic{/t}</a></li>
        <li><a href="#tabs-description">{t}Description{/t}</a></li>
    </ul>

{$form->htmlStart()}

    <div id="tabs-basic">
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('link')}
    </div>
    <div id="tabs-description">
        {$form->htmlFieldWrapped('description')}
    </div>

{$form->htmlEnd()}
</div>