<h1>{$request->getTitle()}</h1>

{if isset($error)}
<div class="alert alert-error">{$msg}</div>
{/if}
{if isset($success)}
<div class="alert alert-success">{$msg}</div>
{/if}
