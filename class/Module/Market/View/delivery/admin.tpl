<table class="table table-striped" id="delivery">
    <thead>
    <tr>
        <th>{t cat="delivery"}Id{/t}</th>
        <th>{t cat="delivery"}Name{/t}</th>
        <th>{t cat="delivery"}Cost{/t}</th>
        <th>{t cat="delivery"}Active{/t}</th>
        <th>{t}Delete{/t}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $items as $item}
    <tr data-id="{$item.id}" class="row-{$item.id}">
        <td>{$item.id}</td>
        <td>{a controller="delivery" action="edit" id=$item.id class="edit"}{$item.name}{/a}</td>
        <td>{$item.cost}</td>
        <td>{if $item.active}{icon name="lightbulb" title=t('On')}
            {else}{icon name="lightbulb_off" title=t('Off')}{/if}</td>
        <td>{a controller="delivery" action="delete" id=$item.id class="do_delete"}{t}Delete{/t}{/a}</td>
    </tr>
    {/foreach}
    </tbody>
</table>

{a controller="delivery" action="edit" class="btn edit"}<i class="icon-plus"></i> {t cat="delivery"}Add delivery method{/t}{/a}