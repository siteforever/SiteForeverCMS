<p>Вы искали: {$query}</p>

<table>
    <tr>
        <th></th>
        <th>Наименование</th>
        <th>Цена</th>
    </tr>
{foreach $goods as $product}
    <tr>
        <td>{$product@index+1}</td>
        <td>{a href=$product->url}{$product->name}{/a}</td>
        <td>{$product->price}</td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="3">Ничего не найдено</td>
    </tr>
{/foreach}
</table>
