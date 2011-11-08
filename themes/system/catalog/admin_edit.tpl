{$breadcrumbs}
<br />
{$form->html()}


<br />
{$gallery_panel}
{*{if $form->cat == "0" && $form->id}*}
{*{include file="system:catgallery/admin_panel.tpl"}*}
{*{/if}*}