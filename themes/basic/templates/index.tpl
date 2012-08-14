<!DOCTYPE html>
<html>
<head>
{head}
</head>
<body class="body">{admin}

<div class="b-body">

    <div class="b-body-wrapper">
    
        {include file="header.tpl"}

        <div class="b-left-panel">
            <div>
                {basket}
            </div>
            <div class="b-left-menu">
                <h3>Карта сайта</h3>
                {menu parent=0 level=5}
            </div>
        </div>

        <div class="b-content">
            {breadcrumbs}
            <h1>{$request->getTitle()}</h1>

            {if $request->getFeedback()}
                <div class="feedback">{$request->getFeedbackString()}</div>
            {/if}
            
            {$request->getEditContent()}
        
        </div>

        <div class="clear"></div>

    </div>
    
    <div class="b-body-footer"></div>

</div>

{include file="footer.tpl"}

</body>
</html>