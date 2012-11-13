/**
 * Скрипт для обработки корзины
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
define([
    "jquery",
    "siteforever",
    "jquery/jquery.form"
],function($, $s){
    return {
        "class_name" : '.basket-widget',

        /**
         * Добавит товар в корзину
         * @param id
         * @param product
         * @param count
         * @param price
         * @param details
         */
        "add" : function( id, product, count, price, details ) {
            return $.post('/?route=basket/add',
                {   basket_prod_id:     id,
                    basket_prod_name:   product,
                    basket_prod_count:  parseInt( count, 10 ),
                    basket_prod_price:  parseFloat( price ),
                    basket_prod_details :details
                }, $.proxy( function( response ){
                    $s.alert( response.msg, 2000 );
                    $(this.class_name).replaceWith( response.widget );
                }, this ),
                "json"
            );
        },

        "init" : function() {
        }
    }
});
