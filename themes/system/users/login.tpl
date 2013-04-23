{if isset($error)}
<div class="alert alert-error">{$msg}</div>
{/if}

{$form->html(false)}

<ul>
    <li>{a href="users/register"}{t cat="user"}Join{/t}{/a}</li>
    <li>{a href="users/restore"}{t cat="user"}Password recovery{/t}{/a}</li>
</ul>
