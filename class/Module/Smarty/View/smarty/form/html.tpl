{include file="smarty/form.tpl"}
{if "form" == $form->vars.type}{*
*}{call name="form_full" form=$form domain=$domain class=$class hint=$hint buttons=$buttons}{*
*}{else}{*
*}{call name="form_row" form=$form domain=$domain class=$class buttons=$buttons}{*
*}{/if}
