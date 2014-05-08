<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
{style file="@theme:less/style.less" filters='less,cssrewrite,?yui_css' output="static/style.css"}
</head>
<body>
<div class="container">
    <header>
        <a class="logo" href="/"></a>
    </header>
    <div class="row">
        <div class="col-sm-4">
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=1 level=3}
            </div>
        </div>
        <div class="col-sm-8 b-content">
            {block breadcrumbs}{/block}

            {if $request->getFeedbackString()}
                <div class="alert alert-success">{$request->getFeedbackString()}</div>
            {/if}

            {$response->getContent()}
        </div>
    </div>
</div>
{js file="@root:components/jquery/jquery.js,@theme:js/script.js" filters='?yui_js'  output="static/script.js"}
{*{js file="@theme:js/script.js" filters='?yui_js'  output="static/script.js"}*}
</body>
</html>
