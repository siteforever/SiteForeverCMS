<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head></head>
<body>
<div class="contaiter">
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
</body>
</html>
