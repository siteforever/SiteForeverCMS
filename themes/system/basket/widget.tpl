            <div class="b-basket basket-widget">
                <img alt="Ваша корзина" src="{$path.images}/basket.png" />
                <div class="b-basket-spacer"></div>
                <div class="b-basket-info">
                    <table class="fullWidth">
                        <tr>
                            <td>ТОВАРОВ:</td>
                            <td class="right">{$count} ШТ.</td>
                        </tr>
                        <tr>
                            <td>СУММА:</td>
                            <td class="right">{$summa|number_format} Р.</td>
                        </tr>
                    </table>
                </div>
                <div class="b-basket-spacer"></div>
                <ul>
                    <li><a {href url="basket"}>ПРОСМОТР &gt;&gt;</a></li>
                    <li><a {href url="order/create"}>ОФОРМИТЬ ЗАКАЗ &gt;&gt;</a></li>
                </ul>
            </div>