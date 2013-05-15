<div class="goods-panel row-fluid">
    {*{html_options name="category" id="goodsCategory" options=$categories selected=$category}*}
    {*{html_options name="type" id="goodsType" options=$types selected=$type}*}
    <div class="span3">
        {a controller="catalog" action="trade" data-action="add" class="btn edit" title=t('catalog','Add product')}
            {icon name="add"} {t cat="catalog"}Add product{/t}
        {/a}
    </div>
    <div class="span6">
        <div class="alert alert-info">
            <strong>Hint:</strong> Выберите в таблице <em>категорию</em> и <em>тип</em> для создания нового товара.
        </div>
    </div>
</div>


{jqgrid name="products" provider=$provider rowNum=20 multiselect=1}