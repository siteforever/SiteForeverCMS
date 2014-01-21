{include file="smarty/form.tpl"}
{call form_start form=$form class=$class domain=$domain}
{$content}
{call form_end}
{$form = $form->setRendered()}
