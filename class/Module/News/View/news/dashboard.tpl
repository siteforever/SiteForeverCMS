<ul>
    <li>Всего категорий: {$catQty} шт.</li>
    <li>Всего статей: {$newsQty} шт.</li>
    <li><strong>За последний месяц:</strong></li>
    {foreach $latestNews as $news}
        <li>{$news.date|date_format:"%d.%m.%Y"} - {a url="news/list" id=$news.cat_id}{$news.name}{/a}</li>
    {/foreach}
</ul>
