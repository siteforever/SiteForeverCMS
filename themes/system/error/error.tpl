<pre class='alert alert-error'><strong>{get_class($error)}</strong>
{$error->getMessage()}
{if App::isDebug()}
    {$error->getFile()} line {$error->getLine()}\n{$error->getTraceAsString()}
{/if}
</pre>
