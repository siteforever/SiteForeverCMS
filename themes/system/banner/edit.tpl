{*<h2>{if $form->id}Правка{else}Создание{/if} баннеров</h2>*}

{form form=$form}
<div id="tabs">
        <ul>
            <li><a href="#tabs-1">{t cat="page"}Main settings{/t}</a></li>
            <li><a href="#tabs-2">{t cat="page"}Content{/t}</a></li>
        </ul>

        <div id="tabs-1">
            {$form->htmlFieldWrapped('id')}
            {$form->htmlFieldWrapped('cat_id')}
            {$form->htmlFieldWrapped('name')}
            {$form->htmlFieldWrapped('url')}
            {$form->htmlFieldWrapped('target')}
        </div>
        <div id="tabs-2">
            {$form->htmlField('content')}
        </div>
</div>
{/form}

{*{$form->html()}*}
