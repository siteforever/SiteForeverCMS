<p>
{a controller="prodtype" action="edit" title=t('catalog','Add type') class="btn edit"}
    {icon name="add"}
    {t cat="catalog"}Add type{/t}
{/a}
</p>

{jqgrid name="prodtype" provider=$provider rowNum=20 multiselect=1}

