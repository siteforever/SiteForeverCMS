<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
    {style file="@misc:jquery/fancybox/jquery.fancybox-1.3.1.css" filters='cssrewrite,?yui_css' output="static/fancybox.css"}
    {style file="@misc:bootstrap/css/bootstrap.css,@theme:css/style.css" filters='cssrewrite,?yui_css' output="static/style.css"}
</head>
<body>
<div class="container">
    {include file="header.tpl"}

    <div class="row">
        <div class="span2">
            <div>
                {basket}
            </div>
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=1 level=3}
            </div>
        </div>
        <div class="span8 b-content">
            {block breadcrumbs}{/block}

            {if $request->getFeedback()}
                <div class="alert">{$request->getFeedbackString()}</div>
            {/if}

            {$response->getContent()}
        </div>
    </div>
</div>
{include file="footer.tpl"}
{js file="@root:components/jquery/jquery.js" filters='?yui_js'  output="static/jquery.js"}
{js file="@misc:jquery/fancybox/jquery.fancybox-1.3.1.js,@theme:js/script.js" filters='?yui_js'  output="static/site.js"}
</body>
</html>
