<ul>
    <li>Всего страниц: {$pageQty} шт.</li>
    <li>Всего скрытых: {$pageHiddenQty} шт.</li>
    <li>Всего удаленных: {$pageDeletedQty} шт.</li>
    <li><strong>За последний месяц:</strong></li>
    {foreach $latestPages as $page}
        <li>{$page.update|date_format:"%d.%m.%Y"} - {a url=$page.alias}{$page.name}{/a}</li>
    {/foreach}
</ul>
