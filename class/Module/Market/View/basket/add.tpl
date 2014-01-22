<div class="b-basket-add">{strip}
    {$item.item}
    <div class="controls">
        <div class="input-append">
            <input type="text" name="basket-prod-count" class="b-basket-add-count input-mini" value="1">
            <button type="button" class="btn b-basket-add-button" value="" data-product="{$item.name}" data-price="{$item.price}" data-id="{$item.id}">
                <i class="icon-shopping-cart"></i>
            </button>
        </div>
    </div>
{/strip}</div>
