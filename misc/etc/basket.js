/**
 * Скрипт для обработки корзины
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

var basket_class    = '.basket-widget';

var basket_add  = function( product, count, price, details )
{
    $.post('/?controller=basket&action=add', {
        basket_prod_id:     product,
        basket_prod_count:  count,
        basket_prod_price:  price,
        basket_prod_details:details
    }, function( data ){
        alert('Товар добавлен в корзину');
        $(basket_class).replaceWith( data );
    });
}

$(function(){
    
})