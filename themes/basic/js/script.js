$(function(){
    $('div.b-request-feedback').delay(5000).fadeOut(1000);
    $('a.gallery').gallery();

    // добавить в корзину
    $('input.basket_add').live('click', function(){
        var product = $(this).attr('data-product');
        var id = $(this).attr('data-id') || product;
        var count   = $(this).parent().find('input.b-product-basket-count').val();
        var price   = $(this).attr('data-price');
        siteforever.basket.add( id, product, count, price, '' );
    });
})