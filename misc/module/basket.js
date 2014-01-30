/**
 * Скрипт для обработки корзины
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */
define("module/basket", [
    "jquery",
    "module/alert",
    "jquery/jquery.form"
],function($, $alert){
    return {
        "class_name" : '.basket-widget',

        /**
         * Additional product to basket
         * @param id Position product in basket
         * @param product string Product name
         * @param count Count for operation
         * @param price Product Price
         * @param details
         * @param callback Function was called for rejected ajax deferred
         */
        "add": function (/*int*/id, /*string*/product, /*int*/count, /*string*/price, details, callback) {
            callback = callback && typeof callback == 'function' ? callback : function (response) {
                $alert(response.msg, 2000);
                $(this.class_name).replaceWith(response.widget);
            };

            return $.post('/basket/add', {
                    basket_prod_id:     id,
                    basket_prod_name:   product,
                    basket_prod_count:  parseInt( count, 10 ),
                    basket_prod_price:  parseFloat( price ),
                    basket_prod_details :details
                }, $.proxy(callback, this),
                "json"
            );
        },

        /**
         * Deletion product from basket
         * @param id Position product in basket
         * @param count Count for operation
         * @param callback Function was called for rejected ajax deferred
         * @returns {*}
         */
        "del": function (/*int*/id, /*int*/count, callback) {
            callback = callback && typeof callback == 'function' ? callback : function (response) {
                $alert( response.msg, 2000 );
                $(this.class_name).replaceWith(response.widget);
            };

            return $.post('/basket/delete', {
                    basket_prod_id:     id,
                    basket_prod_count: parseInt(count, 10)
                }, $.proxy(callback, this),
                "json"
            );
        },

        "init" : function() {
            var basket = this;

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


            // Обработка выбора доставки
            $(document).on('click','#delivery input', function(){
                $('#recalculate').trigger('click');
            });

            $('input.basket-count').on('change', function(){
                if ($(this).val() <= 0) $(this).val(1);
                $('#recalculate').trigger('click');
            });
        }
    }
});
