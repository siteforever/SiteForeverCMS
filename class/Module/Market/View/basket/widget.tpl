<div class="b-basket basket-widget">
    {*<img alt="Ваша корзина" src="{$path.images}/basket.png" />*}
    <h3>{'basket.title'|trans|ucfirst}</h3>
    <div class="b-basket-spacer"></div>
    <div class="b-basket-info">
        <table class="fullWidth">
            <tr>
                <td>{'basket.products'|trans|ucfirst}:</td>
                <td>{$count} {'basket.items'|trans}</td>
            </tr>
            <tr>
                <td>{'basket.sum'|trans|ucfirst}:</td>
                <td>{$summa|number_format} RUR</td>
            </tr>
        </table>
    </div>
    <div class="b-basket-spacer"></div>
    <div><a href="{link url="basket"}">{'basket.view'|trans|ucfirst}</a></div>
</div>
