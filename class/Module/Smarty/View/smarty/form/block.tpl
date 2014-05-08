{*{$form_template = "smarty/form_bs3.tpl"}*}
{include file=$form_template}
{call form_start form=$form class=$class domain=$domain}
{$content}
{call form_end}
{$form = $form->setRendered()}
