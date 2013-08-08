<table class="table">
    <thead>
    <tr>
        <th>{t}Name{/t}</th>
        <th>{t}Desc{/t}</th>
        <th>{t}Module{/t}</th>
        <th>{t}Active{/t}</th>
        <th>{t}Delete{/t}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $item}
    <tr class="row-{$item.id}">
        <td>{a controller="payment" action="edit" id=$item.id class="edit"}{$item.name}{/a}</td>
        <td>{$item.desc}</td>
        <td>{$item.module}</td>
        <td>{if $item.active}{icon name="accept" title=$this->t('Yes')}{else}&nbsp;{/if}</td>
        <td>{a controller="payment" action="delete" id=$item.id class="do_delete"}{t}Delete{/t}{/a}</td>
    </tr>
    {/foreach}
    </tbody>
</table>

{a controller="payment" action="edit" class="btn edit"}<i class="icon-plus"></i> {t cat="payment"}Add payment{/t}{/a}
