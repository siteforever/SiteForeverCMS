{*<p><a {href url="admin/news"}>{t}Category list{/t}</a>*}
{*{if !is_null($cat)} &gt; <a {href controller="news" action="list" id=$cat.id}>{$cat.name}</a>*}
{*<a {href controller="news" action="catedit" id=$cat.id}>{icon name="pencil"}</a>*}
{*{/if}*}
 {*&gt; {t}News edit{/t}</p>*}

{*{$form->html(true,false)}*}

<div id="tabs">
{form form=$form}

    <ul>
        <li><a href="#tabs-1">{t cat="page"}Main settings{/t}</a></li>
        <li><a href="#tabs-2">{t cat="page"}Notice{/t}</a></li>
        <li><a href="#tabs-3">{t cat="page"}Content{/t}</a></li>
    </ul>

    {$form->htmlFieldWrapped('id')}
    {$form->htmlFieldWrapped('author_id')}

    <div id="tabs-1">
        <fieldset>
            <legend>{t cat="page"}Main settings{/t}</legend>
            {$form->htmlFieldWrapped('name')}
            {$form->htmlFieldWrapped('cat_id')}
            {$form->htmlFieldWrapped('date')}
        </fieldset>
        <fieldset>
            <legend>SEO</legend>
            {$form->htmlFieldWrapped('title')}
            {$form->htmlFieldWrapped('keywords')}
            {$form->htmlFieldWrapped('description')}
        </fieldset>
        <fieldset>
            <legend>{t cat="page"}Constraints{/t}</legend>
            {$form->htmlFieldWrapped('hidden')}
            {$form->htmlFieldWrapped('protected')}
            {$form->htmlFieldWrapped('deleted')}
        </fieldset>
    </div>

    <div id="tabs-2">
        {$form->htmlField('notice')}
    </div>

    <div id="tabs-3">
        {$form->htmlField('text')}
    </div>

{/form}
</div>