<div class="goods-panel row">
    {*{html_options name="category" id="goodsCategory" options=$categories selected=$category}*}
    {*{html_options name="type" id="goodsType" options=$types selected=$type}*}
    <div class="col-sm-3">
        {a controller="catalog" action="trade" data-action="add" class="btn edit" title=$this->t('catalog','Add product')}
            {icon name="add"} {t cat="catalog"}Add product{/t}
        {/a}
    </div>
    {*<div class="span6">*}
        {*<div class="form-inline">*}
            {*<span>Групповое действие</span>*}
            {*<input type="text" id="val" name="val" class="input-mini">*}
            {*<select class="input-medium" name="cmd" id="cmd">*}
                {*<option value="mark_top_on">На главную</option>*}
                {*<option value="mark_top_off">Убрать с главной</option>*}
                {*<option disabled="disabled">-</option>*}
                {*<option value="mark_new_on">Новинка</option>*}
                {*<option value="mark_new_off">Не новинка</option>*}
                {*<option disabled="disabled">-</option>*}
                {*<option value="mark_hidden_on">Скрыть</option>*}
                {*<option value="mark_hidden_off">Показать</option>*}
                {*<option disabled="disabled">-</option>*}
                {*<option value="set_price">Проставить цену</option>*}
            {*</select>*}
            {*<button class="btn btn-small">Применить</button>*}
        {*</div>*}
    {*</div>*}
    {*<div class="span6">*}
        {*<div class="alert alert-info">*}
            {*<strong>Hint:</strong> Выберите в таблице <em>категорию</em> и <em>тип</em> для создания нового товара.*}
        {*</div>*}
    {*</div>*}
</div>


{jqgrid name="products" provider=$provider rowNum=20 multiselect=1}
