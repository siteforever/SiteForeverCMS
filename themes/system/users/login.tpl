{if isset($error)}
<div class="alert alert-error">{$msg}</div>
{/if}

{$form->html()}

<div class="btn-group">
    {a href="users/register" class="btn"}{t cat="user"}Join{/t}{/a}
    {a href="users/restore" class="btn"}{t cat="user"}Password recovery{/t}{/a}
</div>
