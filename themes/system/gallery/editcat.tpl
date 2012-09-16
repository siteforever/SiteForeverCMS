{*<h2>{if $form->id}Правка{else}Создание{/if} категории галереи</h2>*}
{$form->html()}
{*<br />*}
{*<p>*}
    {*<a {href  controller="gallery" action="admin"} class="button">{icon name="arrow_left"} Список категорий галерея</a>*}
    {*{if $form->id}*}
        {*<a {href controller="gallery" action="list" id=$form->id} class="button">*}
            {*Изображения в галереи {icon name="arrow_right"}*}
        {*</a>*}
    {*{/if}*}
{*</p>*}
