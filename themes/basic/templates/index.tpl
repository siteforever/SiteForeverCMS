<!DOCTYPE html>
<html>
<head>
{head}
</head>
<body>
{admin}

<div class="contaiter">

    {*<div class="b-body-wrapper">*}
    
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
                {breadcrumbs}

                <h1>{$request->getTitle()}</h1>

                {if $request->getFeedback()}
                    <div class="alert">{$request->getFeedbackString()}</div>
                {/if}

                {$request->getContent()}
            </div>
        </div>
    {*</div>*}
    
    {*<div class="b-body-footer"></div>*}
</div>

{include file="footer.tpl"}

</body>
</html>