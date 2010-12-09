{$breadcrumbs}
<br />
{$form->html()}


<br />
{if $form->cat == "0" && $form->id}
{include file="system:catgallery/admin_panel.tpl"}
{/if}