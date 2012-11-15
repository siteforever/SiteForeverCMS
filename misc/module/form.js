/**
 * Аяксовая обработка формы
 * Зависит от разметки генератора формы SiteForever
 *
 * Принимает от сервиса в json-ответе следующие ключи
 *
 * error - Флаг, указывающий наличие ошибки
 * errors - массив, содержащий строки с описанием ошибок. Проиндексирован по названиям полей
 * redirect - адрес URI, на который скрипт должен перенаправить клиента
 * delivery - исп. при обработке формы доставки. Содержит ключ cost со стоимостью доставки
 * basket - объект, передающий параметры товаров в корзине. Используется на странице корзины
 * basket.d - цифорвые индексы определяют товары в корзине
 * basket.delitems - список индексов товаров, которые были удалены
 * basket.count - количество товаров в корзине
 * basket.sum - сумма товаров в корзине
 */

define([
    "jquery",
    "jquery/jquery.form"
],function($){
    $(document).ready(function(){
        /**
         * Ajax Validate Forms
         */
        $("form.ajax-validate").ajaxForm({
            "method" : "post",
            "iframe" : false,
            "dataType" : "json",
            "success" : function( response, status, xhr, $form ){
                var item;

                if ( response.error ) {
                    $( $form ).find('div.control-group[data-field-name]').each(function(){
                        var errorMsg = response.errors[ $(this).data('field-name') ];
                        if( errorMsg ) {
                            $(this).addClass('error');
                            var divError = $('div.error', this);
                            if ( divError.length ) {
                                divError.html( errorMsg );
                            } else {
                                var divControls = $('div.controls', this),
                                    divMsg = '<div class="error">' + errorMsg + '</div>';
                                divControls.length
                                    ? $(divMsg).insertAfter($(divControls).find(':input'))
                                    : $(this).append(divMsg);
                            }
                        } else {
                            $(this).removeClass('error').find('div.error').remove();
                        }
                    });
                }

                if ( response.basket ) {
                    for ( i in response.basket ) {
                        if ( /^\d+$/.test(i) ) {
                            item = response.basket[i];
                            $('tr[data-key='+i+']').find('.basket-sum')
                                .html( ( parseFloat(item.count) * parseFloat(item.price) ).toFixed(2).replace('.',',') );
                        }
                    }
                    if ( response.basket.delitems ) {
                        for( i in response.basket.delitems ) {
                            $('tr[data-key='+response.basket.delitems[i]+']').remove();
                        }
                    }
                    $('.basket-count','#totalRow').find('b').html( response.basket.count );
                    $('.basket-sum','#totalRow').find('b').html( (response.basket.sum).toFixed(2).replace('.',',') );
                }

                if ( response.delivery && response.delivery.cost ) {
                    $('.basket-sum','#deliveryRow').html( response.delivery.cost );
                }

//                if ( script && script.formResponse && typeof script.formResponse == 'function' ) {
//                    script.formResponse( response );
//                }

                if ( response.redirect ) {
                    window.location.href = response.redirect;
                }

            }
        });
    });
});