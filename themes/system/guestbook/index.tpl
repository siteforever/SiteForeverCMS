{foreach from=$messages item="msg"}
<div class="guestbook_message">
    <div class="guestbook_title">
        <a href="mailto:{$msg->email}">{$msg->name}</a>
        <small>{$msg->date|date_format:"%x"}</small>
    </div>
    <div class="guestbook_body">{$msg->message|strip_tags|nl2br}</div>
    {if $msg->answer}
        <div class="guestbook_answer">
            <strong>{t cat="guestbook"}Answer{/t}:</strong>
            {$msg->answer|strip_tags|nl2br}</div>
    {/if}
</div>
{foreachelse}
<p>Сообщений нет</p>
{/foreach}

<div class="paging">{$paging->html}</div>

{$form->html()}