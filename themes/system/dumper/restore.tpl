<table class="dataset fullWidth">
<tr>
<th colspan="2">{t text="Select dump file for restore"}</th>
</tr>
{foreach from=$files item="f"}
<tr>
    <td><a {href} class="restore_dump" rel="{$f}">{$f}</a></td>
    <td class="right"><a {href} class="delete_dump" rel="{$f}">{t text="Delete dump"}</a></td>
</tr>
{/foreach}
</table>

<p><a {href}>{t text="Back to dumper main page"}</a></p>