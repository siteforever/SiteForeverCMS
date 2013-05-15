{form controller="setting" action="save" class="ajax"}
<div class="tabbable">
    <ul class="nav nav-tabs">
        {foreach $modules as $mod}
        <li{if $mod@first} class="active"{/if}><a href="#{$mod->name}" data-toggle="tab">{$mod->name}</a></li>
        {/foreach}
    </ul>
    <div class="tab-content">
        {foreach $settings as $mod=>$sets}
        <div  class="tab-pane{if $sets@first} active{/if}" id="{$mod}">
            <table class="table table-striped">
            <thead>
                <tr>
                    <th width="150">Свойство</th>
                    <th>Значение</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$sets key="key" item="val"}
                <tr>
                    <td>{$key}</td>
                    <td><input type="text" name="{$mod}[{$key}]" value="{$val}" /></td>
                </tr>
            {/foreach}
            </tbody>
            </table>
        </div>
        {/foreach}
    </div>
</div>
<input type="submit" name="save" class="btn" value="Сохранить" />
{/form}
