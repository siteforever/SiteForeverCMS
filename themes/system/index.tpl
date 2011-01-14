<!DOCTYPE html>
<html>
<head>
<title>{t text="Control panel"} :: {$page.title}</title>

<meta content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ru" />

<link rel="icon" type="image/png" href="http://{$host}/favicon.png" />
<link rel="apple-touch-icon-precomposed" href="http://{$host}/apple-touch-favicon.png" />

<style type="text/css">@import url("{$path.misc}/reset.css");</style>
<style type="text/css">@import url("{$path.misc}/siteforever.css");</style>
<style type="text/css">@import url("{$path.misc}/smoothness/jquery-ui.css");</style>
<style type="text/css">@import url("{$path.misc}/admin.css");</style>

<script type="text/javascript" src="{$path.misc}/jquery.min.js"></script>
<script type="text/javascript" src="{$path.misc}/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$path.misc}/jquery.form.js"></script>
<script type="text/javascript" src="{$path.misc}/jquery.blockUI.js"></script>

{*<script type="text/javascript" src="{$path.misc}/jquery.filemanager.js"></script>*}

{*<script type="text/javascript" src="{$path.misc}/jquery.mousewheel-3.0.2.pack.js"></script>*}
{*<script type="text/javascript" src="{$path.misc}/jquery.easing-1.3.pack.js"></script>*}

{*fancybox*}
<style type="text/css">@import url("{$path.misc}/fancybox/jquery.fancybox-1.3.1.css");</style>
<script type="text/javascript" src="{$path.misc}/fancybox/jquery.fancybox-1.3.1.pack.js"></script>

{*CKEditor*}
<script type="text/javascript" src="{$path.misc}/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{$path.misc}/ckeditor/adapters/jquery.js"></script>

{*<script type="text/javascript" src="{$path.misc}/filebrowser/ajex.js"></script>*}

{*userscript*}
<script type="text/javascript" src="{$path.misc}/forms.js"></script>
<script type="text/javascript" src="{$path.misc}/admin.js"></script>
<script type="text/javascript" src="{$path.misc}/catalog.js"></script>
</head>

<body class="body">
<div class="l-wrapper">

    <h1>{t text="Control panel"} :: {$page.title}</h1>
    {*<div class="b-module-menu">
    </div>*}

    <div class="l-main-panel">
        <div class="l-panel">
	        <ul class="b-admin-menu">
	            {foreach from=$request->get('modules') item="item"}
                <li>
                    {if $item.icon}{icon name=$item.icon}{/if}
                    <a  {if $item.norefact}href="{$item.url}"{else}{href url=$item.url}{/if}
                        {if $item.class!=''}class="{$item.class}"{/if}
                        {if $item.target}target="{$item.target}"{/if} >
                        {$item.name}
                    </a>
                    {if isset($item.sub)}
                        <ul>
                        {foreach from=$item.sub item="subitem"}
                        <li>
                            {if $subitem.icon}{icon name=$subitem.icon}{/if}
                            <a  {if $subitem.norefact}href="{$subitem.url}"{else}{href url=$subitem.url}{/if}
                                {if $subitem.class!=''}class="{$subitem.class}"{/if}
                                {if $subitem.target}target="{$subitem.target}"{/if} >
                                {$subitem.name}
                            </a>
                        </li>
                        {/foreach}
                        </ul>
                    {/if}
                </li>
	            {/foreach}

	        </ul>
        </div>

        <div class="l-content">
            {if $feedback}<p class="red">{$feedback}</p>{/if}

            {$page.content}

            <div class="l-content-wrapper"></div>
        </div>

        <div class="clear"></div>
    </div>

    <div class="l-footer-wrapper"></div>

</div>
<div class="l-footer">
    <a href="http://siteforever.ru" target="_blank">{t text="Working on"} &copy; SiteForeverCMS</a> <small>{t text="Memory"}:{$memory}, {t text="Generation"}:{$exec}</small>
</div>

</body>
</html>