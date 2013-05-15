<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="span1">&nbsp;</th>
            <th>{t}Name{/t}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$rows item="m"}
        <tr>
            <td>{if $m.image}{thumb src=$m.image width=57 height=57 alt=$m.name}{else}&mdash;{/if}</td>
            <td>
                <p><big>{$m.name}</big></p>
                <small>
                    {a url="manufacturers/edit" id=$m.id title=t('Edit manufacturer') class="edit"}
                        {icon name="pencil" title=t("edit")} {t}Edit{/t}{/a}
                    {a url="manufacturers/delete" id=$m.id title=t('Want to delete?') class="do_delete"}
                        {icon name="delete" title=t("delete")} {t}Delete{/t}{/a}
                </small>
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="4">{t}Manufacturers not found{/t}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

<p>{$paging->html}</p>

{a href="manufacturers/edit" class="btn edit" title=t('Create manufacturer')}{t}Create manufacturer{/t}{/a}

{modal id="ManufEdit"}