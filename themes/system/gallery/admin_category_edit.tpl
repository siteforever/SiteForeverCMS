<h2>{if $form->id}Правка{else}Создание{/if} категории галереи</h2>
{$form->html()}
<br />
<p>
    <a {href}>&laquo; Список категорий галерея</a>
    {if $form->id}| <a {href viewcat=$form->id}>Изображения в галереи &raquo;</a>{/if}
</p>
