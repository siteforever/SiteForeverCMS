Здравствуйте!

На сайте {$sitename} был оформлен заказ.

Номер заказа: {$order_n}
Дата:         {$date}
{*Ссылка:       {$ord_link}*}

{foreach from=$positions item="pos"}
Наименование: {$pos.articul}
Цена:         {$pos.price}
Количество:   {$pos.count}
Сумма:        {$pos.price * $pos.count}

{/foreach}

{if $delivery}
Доставка {$delivery->name}
Стоимость {$delivery->cost}
{/if}

Всего: {$total_count}
Сумма: {$total_summa}
