<!DOCTYPE html>
<html lang="{$request->get('lang')}">
<head>
{head}
</head>

<body class="body" id="admin">
<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="container">
            <div class="nav-collapse">
                <span class="brand">SiteForeverCMS</span>
                <ul class="nav">
                    <li><a href="/" target="_blank"><i class="icon-home icon-white"></i> {t}Goto site{/t}</a></li>
                    <li>{a href="setting/admin"}<i class="icon-cog icon-white"></i> {t}Settings{/t}{/a}</li>
                </ul>
                <ul class="nav pull-right">
                    <li>{a href="users/logout"}<i class="icon-remove icon-white"></i> {t}Exit{/t}{/a}</li>
                    {*<li class="dropdown">*}
                        {*{a href="#" htmlData-toggle="dropdown" htmlClass="dropdown-toggle"}User <b class="caret"></b>{/a}*}
                        {*<ul class="dropdown-menu">*}
                            {*<li>{a href="users/logout"}{t}Exit{/t}{/a}</li>*}
                        {*</ul>*}
                        {*<a data-toggle="dropdown" class="dropdown-toggle" href="#">Dropdown <b class="caret"></b></a>*}
                    {*</li>*}
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop in" id="loading-application">
    <div>{t}Initialisation{/t}</div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well">
                <ul class="nav nav-list">
                    {foreach from=$request->get('modules') item="item"}
                    {if isset( $item.url )}
                        {if ! isset( $item.class )}{$item.class = ""}{/if}
                        <li>{a href=$item.url htmlClass=$item.class}{$item.name}{/a}</li>
                    {else}
                        <li class="nav-header">{$item.name}</li>
                    {/if}
                    {if isset($item.sub)}
                        {foreach from=$item.sub item="sitem"}
                            {if ! isset( $sitem.class )}{$sitem.class = ""}{/if}
                            <li>{a href=$sitem.url htmlClass=$sitem.class}{$sitem.name}{/a}</li>
                        {/foreach}
                        <li class="divider"></li>
                    {/if}
                    {/foreach}
                </ul>
            </div>
        </div>

        <div class="span9" id="workspace">
            {*<div class="well">*}
                {if $request->getTitle()}<h2>{$request->getTitle()}</h2>{/if}

                {if $feedback}<div class="alert alert-block">
                    <a class="close" data-dismiss="alert" href="#">&times;</a>
                    {$feedback}
                </div>{/if}

                {$request->getContent()}
            {*</div>*}
        </div>

        <div class="clear"></div>
    </div>

    <div class="l-footer-wrapper"></div>

</div>
{*<footer>*}
    {*<div class="contaiter">*}
        {*<a href="http://siteforever.ru" target="_blank">{t}Working on{/t} &copy; SiteForeverCMS</a>*}
    {*</div>*}
{*</footer>*}

</body>
</html>