<div class="a-gallery" data-url="{link controller="cataloggallery" action="index" id=$cat}">

    {if $request->getFeedback()}<div class="alert alert-error">{$request->getFeedbackString()}</div>{/if}

    <ul class="row-fluid">
        {foreach from=$gallery item="item"}
        <li class="a-gallery-item" data-id="{$item->id}" {if $item.main == 1}style="border-color: red;"{/if}>
            <div class="a-gallery-item-panel">
                {a controller="cataloggallery" action="markdefault" id=$item.id class="main_gallery_image"}
                    {if $item.main}{icon name="star" title=t('catalog','Default')}
                        {else}{icon name="bullet_star" title=t('catalog','Default')}{/if}{/a}
                {a controller="cataloggallery" action="delete" id=$item.id class="del_gallery_image"}
                    {icon name="delete" title=t('catalog','Delete')}{/a}
            </div>
            {thumb width=100 height=100 src=$item.thumb alt=$item.id}
        </li>
        {foreachelse}
        <p>{t cat="catalog"}Images not found{/t}</p>
        {/foreach}
    </ul>

    <div class="clear"></div>
    {*<div class="a-gallery-item-add">*}
        {*{icon name="image_add" title=t('catalog','Add image')}*}
        {*{a controller="cataloggallery" action="upload" prod_id=$cat class="gallery-item-add"}*}
            {*{t cat="catalog"}Add image{/t}{/a}*}
    {*</div>*}

    {*<form method="post" enctype="multipart/form-data" id="catalogGalleryForm" action="{link url="cataloggallery/upload"}">*}
    {if isset($max_file_size)}
    <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}">
    {/if}
    <input type="hidden" name="prod_id" value="{$cat}">
    <input type="hidden" name="sent" value="1">
    <input type="file" class="a-gallery-file" name="image[]" multiple="multiple">
    {*</form>*}

</div>
