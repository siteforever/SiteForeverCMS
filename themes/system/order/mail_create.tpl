Здравствуйте, {$user.fname|default:$user.lname|default:$user.login}

Вы оформили заказ на сайте {$sitename}

Номер заказа: {$order_n}
Дата:         {$date}
Ссылка:       {$ord_link}

{foreach from=$positions item="pos"}
Наименование: {$pos.name}
Цена:         {$pos.price}
Количество:   {$pos.count}
Сумма:        {$pos.summa}

{if $delivery}
Доставка {$delivery->name}
Стоимость {$delivery->cost}
{/if}

{/foreach}
Всего: {$total_count}
Сумма: {$total_summa}

