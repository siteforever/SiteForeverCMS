<h1>{$request->getTitle()}</h1>

{if isset($error) && isset($message)}
<div class="alert alert-error">{$message}</div>
{/if}

{$form->html(false)}

<ul>
    <li>{a href="user/register"}{t cat="user"}Join{/t}{/a}</li>
    <li>{a href="user/restore"}{t cat="user"}Password recovery{/t}{/a}</li>
</ul>
