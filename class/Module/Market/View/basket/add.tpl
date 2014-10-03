<div class="b-basket-add">{strip}
    {$item.item}
    <div class="controls">
        <div class="input-group col-xs-4">
            <input type="text" name="basket-prod-count" class="b-basket-add-count form-control" value="1">
            <span class="input-group-btn">
                <button type="button" class="btn btn-default b-basket-add-button" value="" data-product="{$item.name}" data-price="{$item.price}" data-id="{$item.id}">
                    {"Add basket"|trans}
                </button>
            </span>
        </div>
    </div>
{/strip}</div>
