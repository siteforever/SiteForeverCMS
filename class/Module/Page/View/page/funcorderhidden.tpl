{function orderHidden}
{a controller="page" action="hidden" id=$page->id class="order_hidden"}
    {if $page->hidden}{icon name="lightbulb_off" title=$this->t('Off')} {t}Off{/t}
    {else}{icon name="lightbulb" title=$this->t('On')} {t}On{/t}{/if}{/a}
{/function}
