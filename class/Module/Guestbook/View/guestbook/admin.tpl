<table class="table table-striped">
    <thead>
    <tr>
        <th>&nbsp;</th>
        <th>{t cat="guestbook"}Name{/t}</th>
        <th>{t cat="guestbook"}Email{/t}</th>
        <th>{t cat="guestbook"}Message{/t}</th>
        <th>{t cat="guestbook"}Category{/t}</th>
        <th>{t cat="guestbook"}Answer{/t}</th>
        <th>{t cat="guestbook"}Date{/t}</th>
        <th>{t cat="guestbook"}IP{/t}</th>
    </tr>
    </thead>
{foreach from=$list item="item"}
    <tr>
        <td><i class="{if $item->hidden}icon-eye-close{else}icon-eye-open{/if}"></i></td>
        <td>{$item->name}</td>
        <td><a href="mailto:{$item->email}">{$item->email}</a></td>
        <td>{a controller="guestbook" action="edit" id=$item->id htmlTarget="_blank" class="sfcms_guestbook_edit"}
            {$item->message|truncate:50}{icon name="pencil" title=$this->t('Edit')}{/a}
        </td>
        <td>{a controller="guestbook" action="admin" link=$item->link}
            {$item->Category->name}{/a}</td>
        <td>{if $item->answer}{icon name="accept" title=$this->t('guestbook','Answer')}{/if}</td>
        <td>{$item->date|date_format:"%d.%m.%Y (%H:%M)"}</td>
        <td>{$item->ip}</td>
    </tr>
{/foreach}
</table>

{$paging->html}
