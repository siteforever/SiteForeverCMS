Здравствуйте, {$order->getEmptorName()}

Вы оформили заказ на сайте {$sitename}

Номер заказа: {$order_n}
Дата:         {$date}
Ссылка для просмотра заказа:
{$ord_link}

{foreach from=$positions item="pos"}
Наименование: {$pos.articul}
Цена:         {$pos.price}
Количество:   {$pos.count}
Сумма:        {$pos.price * $pos.count}

{/foreach}

{if $delivery}
Доставка:  {$delivery->getObject()->name}
Стоимость: {$delivery->cost($sum)}
Адрес:     {$order->address}
{/if}

Всего: {$total_count}
Сумма: {$total_summa}
