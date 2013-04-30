/**
 * Скрипт для обработки корзины
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
define([
    "jquery",
    "module/alert",
    "jquery/jquery.form"
],function($, alert){
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
        "add": function (id, product, count, price, details, callback) {
            callback = callback && typeof callback == 'function' ? callback : function( response ){
                alert( response.msg, 2000 );
                $(this.class_name).replaceWith( response.widget );
            };

            return $.post('/?route=basket/add', {
                    basket_prod_id:     id,
                    basket_prod_name:   product,
                    basket_prod_count:  parseInt( count, 10 ),
                    basket_prod_price:  parseFloat( price ),
                    basket_prod_details :details
                }, $.proxy( callback, this ),
                "json"
            );
        },

        "init" : function() {

            // basket handler
            $(document).on('sfcms.form.success', function( event, response ){
                var item;
                if ( response.basket ) {
                    for ( var i in response.basket ) {
                        if ( /^\d+$/.test(i) ) {
                            item = response.basket[i];
                            $('tr[data-key='+i+']').find('.basket-sum')
                                .html( ( parseFloat(item.count) * parseFloat(item.price) ).toFixed(2).replace('.',',') );
                        }
                    }
                    if ( response.basket.delitems ) {
                        for( i in response.basket.delitems ) {
                            $('tr[data-key=' + response.basket.delitems[i] + ']').remove();
                        }
                    }
                    $('.basket-count','#totalRow').find('b').html( response.basket.count );
                    $('.basket-sum','#totalRow').find('b').html( (response.basket.sum).toFixed(2).replace('.',',') );
                }
            });

            // delivery handler
            $(document).on('sfcms.form.success', function( event, response ){
                if ( response.delivery && response.delivery.cost ) {
                    $('.basket-sum','#deliveryRow').html( response.delivery.cost );
                }
            });

        }
    }
});
