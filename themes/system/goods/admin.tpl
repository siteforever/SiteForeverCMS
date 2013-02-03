<div class="goods-panel">
{*{html_options name="category" id="goodsCategory" options=$categories selected=$category}*}
{*{html_options name="type" id="goodsType" options=$types selected=$type}*}
{a controller="catalog" action="trade" data-action="add" class="btn edit" title=t('catalog','Add product')}{icon name="add"} {t cat="catalog"}Add product{/t}{/a}
</div>


{jqgrid name="products" provider=$provider rowNum=20 multiselect=1}