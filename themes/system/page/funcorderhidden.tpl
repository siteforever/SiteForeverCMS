{function orderHidden}
{a controller="page" action="hidden" id=$page->id class="order_hidden"}
    {if $page->hidden}{icon name="lightbulb_off" title=t('Off')}
    {else}{icon name="lightbulb" title=t('On')}{/if}{/a}
{/function}