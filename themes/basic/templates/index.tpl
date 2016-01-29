<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
<link rel="stylesheet" href="/static/lib/fancybox/source/jquery.fancybox.css" />
<link rel="stylesheet" href="/static/lib/bootstrap/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="/static/lib/bootstrap/dist/css/bootstrap-theme.min.css" />
<link rel="stylesheet" href="/themes/basic/css/style.css" />
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
<script type="text/javascript" src="/static/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="/static/lib/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/lib/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="/themes/basic/js/script.js"></script>
</body>
</html>
