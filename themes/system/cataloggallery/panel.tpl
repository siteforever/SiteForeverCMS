<div class="a-gallery" data-url="{link controller="cataloggallery" action="index" id=$cat}">

    {*<h2>{icon name="images" title=t('catalog','Gallery')} {t cat="catalog"}Gallery{/t}</h2>*}

    <div class="row-fluid">
        {foreach from=$gallery item="item"}
        <div class="a-gallery-item" {if $item.main == 1}style="border-color: red;"{/if}>
            <div>
                {thumb width=100 height=100 src=$item.image alt=$item.id}
                <div>
                    {a controller="cataloggallery" action="markdefault" id=$item.id class="main_gallery_image"}
                        {if $item.main}{icon name="star" title=t('catalog','Default')}
                            {else}{icon name="bullet_star" title=t('catalog','Default')}{/if}{/a}
                    {a controller="cataloggallery" action="delete" id=$item.id class="del_gallery_image"}
                        {icon name="delete" title=t('catalog','Delete')}{/a}
                </div>
            </div>
        </div>
        {foreachelse}
        <p>{t cat="catalog"}Images not found{/t}</p>
        {/foreach}
    </div>

    <div class="clear"></div>
    <div class="a-gallery-item-add">
        {icon name="image_add" title=t('catalog','Add image')}
        {a controller="cataloggallery" action="upload" prod_id=$cat class="gallery-item-add"}
            {t cat="catalog"}Add image{/t}{/a}
    </div>
</div>