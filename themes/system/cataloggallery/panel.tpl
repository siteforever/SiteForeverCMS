<div class="a-gallery">

    <h2>{icon name="images" title="Галлерея"} Галлерея</h2>

    {foreach from=$gallery item="item"}
    <div class="a-gallery-item" {if $item.main == 1}style="border-color: red;"{/if}>
        <div>
            <img width="100" height="100" src="{$item.thumb}" alt="{$item.id}" title="{$item.image}" />
            <div>
                <a {href controller="cataloggallery" action="markdefault" id=$item.id} class="main_gallery_image">
                    {if $item.main}{icon name="star" title="По умолчанию"}{else}{icon name="bullet_star" title="По умолчанию"}{/if}</a>
                <a {href controller="cataloggallery" action="delete" id=$item.id} class="del_gallery_image">{icon name="delete" title="Удалить"}</a>
            </div>
        </div>
    </div>
    {foreachelse}
    <p>Изображения не найдены</p>
    {/foreach}

    <div class="clear"></div>
    <div class="a-gallery-item-add">
        {icon name="image_add" title="Добавить изображение"}
        <a {href controller="cataloggallery" action="upload" prod_id=$cat} class="gallery-item-add">Добавить изображение</a>
    </div>
    <div class="clear"></div>
</div>