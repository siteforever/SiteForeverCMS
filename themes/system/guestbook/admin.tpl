<table class="dataset">
    <tr>
        <th>Id</th>
        <th>{t cat="guestbook"}Name{/t}</th>
        <th>{t cat="guestbook"}Email{/t}</th>
        <th>{t cat="guestbook"}Category{/t}</th>
        <th>{t cat="guestbook"}Message{/t}</th>
        <th>{t cat="guestbook"}Answer{/t}</th>
        <th>{t cat="guestbook"}Date{/t}</th>
        <th>{t cat="guestbook"}IP{/t}</th>
    </tr>
{foreach from=$list item="item"}
    <tr>
        <td>{$item->id}</td>
        <td>{$item->name}</td>
        <td>{$item->email}</td>
        <td>{a controller="guestbook" action="admin" link=$item->link}
            {$item->Category->name}{/a}</td>
        <td>{a controller="guestbook" action="edit" id=$item->id htmlTarget="_blank" class="sfcms_guestbook_edit"}
            {$item->message|truncate:50}{/a}
        </td>
        <td>{if $item->answer}{icon name="accept" title=t('guestbook','Answer')}{/if}</td>
        <td>{$item->date|date_format:"%d.%m.%Y (%H:%M)"}</td>
        <td>{$item->ip}</td>
    </tr>
{/foreach}
</table>

{$paging->html}
