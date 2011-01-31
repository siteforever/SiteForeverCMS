<!DOCTYPE html>
<html>
<head>
{head}
</head>
<body class="body">

<div class="b-body">

    <div class="b-body-wrapper">
    
        {include file="header.tpl"}


        <div class="b-left-panel">
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=0 level=5}
            </div>

            {if isset($page.controller) && $page.controller == 'catalog'}
            <div class="b-left-catmenu">
                <h3>Каталог</h3>
                {catmenu parent=0 level=2 url=$page.alias}
            </div>
            {/if}
        </div>

        <div class="b-content">
        
            {breadcrumbs page=$page}

            <h1>{$page.title|default:$page.name}</h1>

            {$feedback}
            
            {$page.content}
        
        </div>

        <div class="clear"></div>

    </div>
    
    <div class="b-body-footer"></div>

</div>

{include file="footer.tpl"}

</body>
</html>