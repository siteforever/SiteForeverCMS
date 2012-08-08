<div class="b-basket basket-widget">
    <img alt="Ваша корзина" src="{$path.images}/basket.png" />
    <div class="b-basket-spacer"></div>
    <div class="b-basket-info">
        <table class="fullWidth">
            <tr>
                <td>Товаров:</td>
                <td>{$count} шт.</td>
            </tr>
            <tr>
                <td>{t}Sum{/t}:</td>
                <td>{$summa|number_format} руб.</td>
            </tr>
        </table>
    </div>
    <div class="b-basket-spacer"></div>
    <ul>
        <li><a {href url="basket"}>Просмотр &gt;&gt;</a></li>
        <li><a {href url="order/create"}>Оформить заказ &gt;&gt;</a></li>
    </ul>
</div>