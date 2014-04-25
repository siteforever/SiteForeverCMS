<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
{*{css file="theme:less/style.less"}*}
{*{js file="@jquery"}*}
{*{js file="theme:js/script.js"}*}
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-4">
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=1 level=3}
            </div>
        </div>
        <div class="col-sm-8 b-content">
            {block breadcrumbs}{/block}

            {if $request->getFeedback()}
                <div class="alert alert-success">{$request->getFeedbackString()}</div>
            {/if}

            {$response->getContent()}
        </div>
    </div>
</div>
</body>
</html>
