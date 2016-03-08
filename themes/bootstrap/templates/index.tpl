<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
{style file="static/lib/bootstrap/dist/css/bootstrap.min.css"}
{style file="static/lib/bootstrap/dist/css/bootstrap-theme.min.css"}
</head>
<body>
<div class="container">
    <header>
        <a class="logo" href="/"></a>
    </header>
    <div class="row">
        <div class="col-sm-3">
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=1 level=3}
            </div>
        </div>
        <div class="col-sm-6 b-content">
            {block breadcrumbs}{/block}

            {if $request->getFeedbackString()}
                <div class="alert alert-success">{$request->getFeedbackString()}</div>
            {/if}

            {$response->getContent()}
        </div>
        <div class="col-sm-3">
            {basket}
        </div>
    </div>
</div>
{*<script type="text/javascript" src="/static/lib/jquery/dist/jquery.min.js"></script>*}
{*<script type="text/javascript" src="/static/lib/bootstrap/dist/js/bootstrap.min.js"></script>*}
{*<script type="text/javascript" src="/themes/bootstrap/js/script.js"></script>*}

{js file="static/lib/jquery/dist/jquery.min.js"}
{js file="static/lib/bootstrap/dist/js/bootstrap.min.js"}
{js file="@theme:js/script.js"}
</body>
</html>
