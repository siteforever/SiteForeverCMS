<table class="dataset">
    <tr>
        <th>Id</th>
        <th>{t}Name{/t}</th>
        <th>{t}Email{/t}</th>
        <th>{t}Date{/t}</th>
        <th>{t}IP{/t}</th>
    </tr>
{foreach from=$messages item="msg"}
    <tr>
        <td>{$msg->id}</td>
        <td><a href="/guestbook/edit/id/{$msg->id}" target="_blank" class="sfcms_guestbook_edit">{$msg->name}</a></td>
        <td>{$msg->email}</td>
        <td>{$msg->date|date_format:"%x"}</td>
        <td>{$msg->ip}</td>
    </tr>
{/foreach}
</table>

{$paging->html}
