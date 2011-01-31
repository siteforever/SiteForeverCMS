<!DOCTYPE html>
<html>
<head>
{head}
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
                    {if ! empty($item.icon)}{icon name=$item.icon}{/if}
                    <a  {if ! empty($item.norefact)}href="{$item.url}"{else}{href url=$item.url}{/if}
                        {if ! empty($item.class)}class="{$item.class}"{/if}
                        {if ! empty($item.target)}target="{$item.target}"{/if} >
                        {$item.name}
                    </a>
                    {if isset($item.sub)}
                        <ul>
                        {foreach from=$item.sub item="subitem"}
                        <li>
                            {if ! empty($subitem.icon)}{icon name=$subitem.icon}{/if}
                            <a  {if ! empty($subitem.norefact)}href="{$subitem.url}"{else}{href url=$subitem.url}{/if}
                                {if ! empty($item.class)}class="{$subitem.class}"{/if}
                                {if ! empty($subitem.target)}target="{$subitem.target}"{/if} >
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