<h1>{$request->getTitle()}</h1>

{if $error}
<div class="alert alert-error">
    {$msg}
</div>
{else}
<div class="alert alert-success">
    {$msg}
</div>
{/if}
