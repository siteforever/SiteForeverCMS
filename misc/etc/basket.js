/**
 * Скрипт для обработки корзины
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
siteforever.basket  = {};

siteforever.basket.class_name    = '.basket-widget';

/**
 * Добавит товар в корзину
 * @param id
 * @param product
 * @param count
 * @param price
 * @param details
 */
siteforever.basket.add  = function( id, product, count, price, details )
{
    $.post('/?controller=basket&action=add', {
        basket_prod_id:     id,
        basket_prod_name:   product,
        basket_prod_count:  parseInt( count ),
        basket_prod_price:  parseFloat( price ),
        basket_prod_details:details
    }, function( data ){
        //siteforever.alert('Товар добавлен в корзину');
        $(siteforever.basket.class_name).replaceWith( data );
    });
}