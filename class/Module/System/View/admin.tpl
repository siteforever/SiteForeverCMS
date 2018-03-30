<!DOCTYPE html>
<html lang="{$request->getLocale()}">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
{*{style file="assets/admin/admin.src.css"}*}
{style file="assets/admin/admin.css"}
</head>
<body class="body" id="admin">
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<nav class="navbar navbar-inverse navbar-top">{strip}
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-cms-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="/" class="navbar-brand" title="{'Goto site'|trans} {$sitename}" target="_blank">
            <i class="glyphicon glyphicon-home"></i> CMS
        </a>
    </div>
    <div class="collapse navbar-collapse" id="bs-cms-navbar-collapse">

        <ul class="nav navbar-nav navbar-right">
            <li>{$user}</li>
            <li>{a href="user/logout"}<i class="glyphicon glyphicon-off"></i> {'Exit'|trans}{/a}</li>
        </ul>
    </div>
{/strip}</nav>
<div class="modal-backdrop in" id="loading-application">
    <div>{'Initialisation'|trans}</div>
</div>



<div class="wrapper">
    <div class="left-column">
        <ul class="nav nav-pills nav-stacked">{strip}
            {foreach from=$request->get('modules') item="item"}
                {if ! isset( $item.class )}{$item.class = ""}{/if}
                {if isset($item.sub)}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle text-center {$item.class}" data-toggle="dropdown" title="{$item.name|trans|ucfirst}">
                            <i class="glyphicon glyphicon-{if isset($item.glyph)}{$item.glyph}{else}folder-close{/if}"></i>
                            &nbsp;<b class="caret"></b>
                            <div class="small">{$item.name|trans|ucfirst}</div>
                        </a>
                        <ul class="dropdown-menu">
                            {foreach from=$item.sub item="sitem"}
                                {if ! isset( $sitem.class )}{$sitem.class = ""}{/if}
                                <li>{a href=$sitem.url htmlClass=$sitem.class title=$sitem.name|trans|ucfirst}
                                        <i class="glyphicon glyphicon-{if isset($item.glyph)}{$item.glyph}{else}folder-close{/if}"></i>
                                        &nbsp;{$sitem.name|trans|ucfirst}
                                    {/a}</li>
                            {/foreach}
                        </ul>
                    </li>
                {else}
                    <li>
                        <a href="/{$item.url|default:""}" class="{$item.class} text-center" title="{$item.name|trans|ucfirst}">
                            <i class="glyphicon glyphicon-{if isset($item.glyph)}{$item.glyph}{else}folder-close{/if}"></i>
                            <div class="small">{$item.name|trans|ucfirst}</div>
                        </a>
                    </li>
                {/if}
            {/foreach}
        {/strip}</ul>
    </div>
    <div class="right-column" id="workspace">

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
        <div class="clear"></div>
    </div>

</div>

</body>
</html>
{style file="static/admin/jquery/elfinder/elfinder.css"}
{style file="static/system/icons.css"}
