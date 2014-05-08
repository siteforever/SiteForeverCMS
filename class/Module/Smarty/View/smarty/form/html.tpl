{*{$form_template = "smarty/form_bs3.tpl"}*}
{include file=$form_template}
{if "form" == $form->vars.type}{*
*}{call name="form_full" form=$form domain=$domain class=$class hint=$hint buttons=$buttons}{*
*}{else}{*
*}{call name="form_row" form=$form domain=$domain class=$class buttons=$buttons}{*
*}{/if}
