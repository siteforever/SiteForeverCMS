{$breadcrumbs}
<br />
{$form->html()}


<br />
{if isset($gallery_panel)}{$gallery_panel}{/if}
{*{if $form->cat == "0" && $form->id}*}
{*{include file="system:catgallery/admin_panel.tpl"}*}
{*{/if}*}