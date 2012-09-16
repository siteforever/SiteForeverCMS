<form action="/?controller=settings&action=save" method="post" class="ajax">
    <div id="tabs">
        <ul>
            {foreach from=$modules item="mod"}
            <li><a href="#tabs-{$mod->name}">{$mod->title}</a></li>
            {/foreach}
        </ul>
        {foreach from=$settings key="mod" item="sets"}
        <div id="tabs-{$mod}">
            <table class="table table-striped">
            <thead>
                <tr>
                    <th width="150">Свойство</th>
                    <th>Значение</th>
                </tr>
            </thead>
            {foreach from=$sets key="key" item="val"}
            <tr>
                <td>{$key}</td>
                <td><input type="text" name="{$mod}[{$key}]" value="{$val}" /></td>
            </tr>
            {/foreach}
            </table>
        </div>
        {/foreach}

        <input type="submit" name="save" class="btn" value="Сохранить" />
    </div>
</form>