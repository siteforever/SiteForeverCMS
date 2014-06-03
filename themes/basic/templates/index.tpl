<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
    {style file="@root:static/system/jquery/fancybox/jquery.fancybox-1.3.1.css" filters='cssrewrite,?yui_css' output="static/site/fancybox.css"}
    {style file="@root:components/bootstrap/css/bootstrap.css,@theme:css/style.css" filters='cssrewrite,?yui_css' output="static/site/style.css"}
</head>
<body>
<div class="container">
    {include file="header.tpl"}

    <div class="row">
        <div class="col-xs-3">
            <div>
                {basket}
            </div>
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=1 level=3}
            </div>
        </div>
        <div class="col-xs-9 b-content">
            {block breadcrumbs}{/block}

            {if $request->hasFeedback('error')}
                <div class="alert alert-danger">{$request->getFeedbackString('<br>', 'error')}</div>
            {/if}
            {if $request->hasFeedback('default')}
                <div class="alert alert-info">{$request->getFeedbackString('<br>', 'default')}</div>
            {/if}
            {if $request->hasFeedback('success')}
                <div class="alert alert-success">{$request->getFeedbackString('<br>', 'success')}</div>
            {/if}

            {$response->getContent()}
        </div>
    </div>
</div>
{include file="footer.tpl"}
{js file="@root:components/jquery/jquery.js" filters='?yui_js'  output="static/site/jquery.js"}
{js file="@root:static/system/jquery/fancybox/jquery.fancybox-1.3.1.js,@theme:js/script.js" filters='?yui_js'  output="static/site/script.js"}
</body>
</html>
