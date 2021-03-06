<h1>{$request->getTitle()}</h1>

{foreach from=$messages item="msg"}
<div class="well">
    <p class="guestbook_title">
        <strong>{$msg->name|escape}</strong>
        <small>{$msg->date|date_format:"%x"}</small>
    </p>
    <p class="guestbook_body">{$msg->message|strip_tags|escape|nl2br}</p>
    {if $msg->answer}
        <div class="guestbook_answer">
            <p><strong>{t cat="guestbook"}Answer{/t}:</strong></p>
            {$msg->answer|strip_tags|escape|nl2br}</div>
    {/if}
</div>
{foreachelse}
<p>Сообщений нет</p>
{/foreach}

<div class="paging">{$paging->html}</div>
{$form->html([domain=>"guestbook", class=>"form-horizontal", hint=>false, buttons=>true])}
