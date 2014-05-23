<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
{style file=[
    "@root:components/bootstrap/css/bootstrap.css",
    "@root:static/admin/jquery/jqgrid/ui.jqgrid.css",
    "@root:static/system/icons.css",
    "@root:static/system/admin.css"
] filters="cssrewrite,?yui_css" output="static/css/admin.css"}
</head>
<body class="body" id="admin">
<div class="navbar navbar-inverse navbar-static-top">{strip}
    <div class="container-fluid">
        <div class="navbar-header">
            <span class="navbar-brand">SiteForeverCMS</span>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="/" target="_blank"><i class="icon-home icon-white"></i> {'Goto site'|trans}</a></li>
            {*<li>{a href="setting/admin"}<i class="icon-cog icon-white"></i> {'Settings'|trans}{/a}</li>*}
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li>{a href="user/logout"}<i class="icon-remove icon-white"></i> {'Exit'|trans}{/a}</li>
        </ul>
    </div>
{/strip}</div>
<div class="modal-backdrop in" id="loading-application">
    <div>{'Initialisation'|trans}</div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="well">
                <ul class="nav nav-list">{strip}
                    {foreach from=$request->get('modules') item="item"}
                    {if isset( $item.url )}
                        {if ! isset( $item.class )}{$item.class = ""}{/if}
                        <li>{a href=$item.url htmlClass=$item.class}{$item.name|trans|ucfirst}{/a}</li>
                    {else}
                        <li class="nav-header">{$item.name|trans|ucfirst}</li>
                    {/if}
                    {if isset($item.sub)}
                        {foreach from=$item.sub item="sitem"}
                            {if ! isset( $sitem.class )}{$sitem.class = ""}{/if}
                            <li>{a href=$sitem.url htmlClass=$sitem.class}{$sitem.name|trans|ucfirst}{/a}</li>
                        {/foreach}
                        <li class="divider"></li>
                    {/if}
                    {/foreach}
                {/strip}</ul>
            </div>
        </div>

        <div class="col-md-10" id="workspace">
            <!--[if lt IE 9]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->

            {if $request->getTitle() && empty($title)}<h1>{$request->getTitle()|trans|ucfirst}</h1>{/if}

            {if $feedback}<div class="alert alert-block">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                {$feedback}
            </div>{/if}

            {$response->getContent()}
        </div>

        <div class="clear"></div>
    </div>

    <div class="l-footer-wrapper"></div>

</div>
</body>
</html>
