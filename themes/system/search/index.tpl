<p>Вы искали: <b>{$sword}</b></p>
<hr />
{foreach from=$list key="key" item="item"}
<div>
    <h3>{counter}. <a {href url="catalog" cat=$item.id}>{$item.name}</a></h3>
    <p>Артикул: {$item.articul}</p>
    <p>{$item.text|strip_tags|truncate:200}</p>
</div>
<hr />
{foreachelse}
<div>Ничего не найдено</div>
{/foreach}