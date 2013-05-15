<div id="adminPanel">
{t}SiteForever:{/t}
{*{$request->get('controller')}::{$request->get('action')}*}
{if 'page' == $request->get('controller')}
    {a controller=$request->get('controller') action='edit' edit=$page->id class="edit"}{t}Edit{/t}{/a}
{/if}

{a controller='user' action='logout' class="float_right"}{icon name="door_in" title=t('Exit')} {t}Exit{/t}{/a}
</div>
