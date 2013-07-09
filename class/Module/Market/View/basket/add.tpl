<div class="b-basket-add">
    {$item.item}
    <div class="input-append">
        <input type="text" name="basket-prod-count" class="b-basket-add-count span1" value="1" />
        <input type="button" class="btn b-basket-add-button" value="{t cat="basket"}Add to basket{/t}"
               data-product="{$item.name}" data-price="{$item.price}" data-id="{$item.id}" />
    </div>
</div>
