<table class="table">
    <thead>
    <tr>
        <th>{t}Name{/t}</th>
        <th>{t}Desc{/t}</th>
        <th>{t}Module{/t}</th>
        <th>{t}Active{/t}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $list as $item}
    <tr>
        <td>{a controller="payment" action="edit" id=$item.id class="edit"}{$item.name}{/a}</td>
        <td>{$item.desc}</td>
        <td>{$item.module}</td>
        <td>{if $item.active}{icon name="accept" title=t('Yes')}{else}&nbsp;{/if}</td>
    </tr>
    {/foreach}
    </tbody>
</table>