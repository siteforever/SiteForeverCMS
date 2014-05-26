<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
{style file=[
    "@root:components/bootstrap/css/bootstrap.css",
    "@root:components/bootstrap/css/bootstrap-theme.css"
] filters="cssrewrite,?yui_css" output="static/css/bootstrap.css"}
{style file=[
    "@root:components/jquery-ui/themes/flick/jquery-ui.css",
    "@root:static/admin/jquery/jqgrid/ui.jqgrid.css",
    "@root:static/admin/jquery/elfinder/elfinder.css"
] filters="cssrewrite,?yui_css" output="static/css/jquery-ui.css"}
{style file=[
    "@root:static/system/icons.css",
    "@root:static/system/admin.css"
] filters="cssrewrite,?yui_css" output="static/css/admin.css"}
</head>
<body class="body" id="admin">
<div class="navbar navbar-inverse">{strip}
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-cms-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/" class="navbar-brand" title="{'Goto site'|trans}" target="_blank"><i class="glyphicon glyphicon-home"></i> {$sitename}</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-cms-navbar-collapse">
            <ul class="nav navbar-nav">{strip}
                    {foreach from=$request->get('modules') item="item"}
                        {if ! isset( $item.class )}{$item.class = ""}{/if}
                        {if isset($item.sub)}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle {$item.class}" data-toggle="dropdown">
                                    {if isset($item.gliph)}<i class="glyphicon glyphicon-{$item.gliph}"></i> {/if}
                                    {$item.name|trans|ucfirst} <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    {foreach from=$item.sub item="sitem"}
                                        {if ! isset( $sitem.class )}{$sitem.class = ""}{/if}
                                        <li>{a href=$sitem.url htmlClass=$sitem.class}
                                            {if isset($sitem.gliph)}<i class="glyphicon glyphicon-{$sitem.gliph}"></i> {/if}
                                            {$sitem.name|trans|ucfirst}{/a}</li>
                                    {/foreach}
                                </ul>
                            </li>
                        {else}
                            <li>
                                <a href="/{$item.url|default:""}" class="{$item.class}">
                                    {if isset($item.gliph)}<i class="glyphicon glyphicon-{$item.gliph}"></i> {/if}
                                    {$item.name|trans|ucfirst}</a>
                            </li>
                        {/if}
                    {/foreach}
                {/strip}</ul>
            <ul class="nav navbar-nav navbar-right">
                <li>{a href="user/logout"}<i class="glyphicon glyphicon-off"></i> {'Exit'|trans}{/a}</li>
            </ul>
        </div>
    </div>
{/strip}</div>
<div class="modal-backdrop in" id="loading-application">
    <div>{'Initialisation'|trans}</div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-offset-1 col-md-10" id="workspace">
            <!--[if lt IE 9]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->

            {if $request->getTitle() && empty($title)}<h1>{$request->getTitle()|trans|ucfirst}</h1>{/if}

            {if $this->hasFlash('default')}<div class="alert alert-info">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                {foreach $this->getFlash('default') as $message}
                    {$message}{if not $message@last}<br>{/if}
                {/foreach}
            </div>{/if}
            {if $this->hasFlash('success')}<div class="alert alert-success">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                {foreach $this->getFlash('success') as $message}
                    {$message}{if not $message@last}<br>{/if}
                {/foreach}
            </div>{/if}
            {if $this->hasFlash('warning')}<div class="alert alert-warning">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                {foreach $this->getFlash('warning') as $message}
                    {$message}{if not $message@last}<br>{/if}
                {/foreach}
            </div>{/if}
            {if $this->hasFlash('error')}<div class="alert alert-danger">
                <a class="close" data-dismiss="alert" href="#">&times;</a>
                {foreach $this->getFlash('error') as $message}
                    {$message}{if not $message@last}<br>{/if}
                {/foreach}
            </div>{/if}

            {$response->getContent()}
        </div>

        <div class="clear"></div>
    </div>

    <div class="l-footer-wrapper"></div>

</div>
</body>
</html>
