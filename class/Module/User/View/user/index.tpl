<h1>{$request->getTitle()}</h1>

{if !empty($error)}
<div class="alert alert-error">{$message}</div>
{/if}
{if !empty($success)}
<div class="alert alert-success">{$message}</div>
<p>Теперь можете {a href="user/login"}войти на сайт{/a}</p>
{/if}
