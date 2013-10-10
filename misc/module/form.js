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

define("module/form",[
    "jquery",
    "jquery/jquery.form"
],function($){

    // error handler
    $(document).on('sfcms.form.success', function( event, response, status, xhr, $form ) {
        if ( response.error ) {
            $form.find('div.control-group[data-field-name]').each(function(){
                var $this = $(this);
                var errorMsg = response.errors[ $this.data('field-name') ];
                if( errorMsg ) {
                    $this.addClass('error');
                    var divError = $('div.error', this);
                    if ( divError.length ) {
                        divError.html( errorMsg );
                    } else {
                        var divControls = $('div.controls', this),
                            divMsg = '<div class="error">' + errorMsg + '</div>';
                        divControls.length
                            ? $(divMsg).insertAfter($(divControls).find(':input'))
                            : $this.append(divMsg);
                    }
                } else {
                    $this.removeClass('error').find('div.error').remove();
                }
            });
        }
    });

    // redirect handler
    $(document).on('sfcms.form.success', function( event, response ){
        if ( response.redirect ) {
            event.stopPropagation();
            window.location.href = response.redirect;
        }
    });

    $(document).ready(function(){
        /**
         * Ajax Validate Forms
         */
        $("form.ajax-validate").ajaxForm({
            method : "post",
            iframe : false,
            dataType : "json",
            success: function (response, status, xhr, $form) {
                //1.) responseText or responseXML value (depending on the value of the dataType option).
                //2.) statusText
                //3.) xhr (or the jQuery-wrapped form element if using jQuery < 1.4)
                //4.) jQuery-wrapped form element (or undefined if using jQuery < 1.4)

                $(document).trigger('sfcms.form.success', arguments);
            },
            beforeSubmit: function(arr, $form, options) {
                // The array of form data takes the following form:
                // [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]

                // return false to cancel submit
                $(document).trigger('sfcms.form.beforeSubmit', arguments);
            },
            error: function() {
                $(document).trigger('sfcms.form.error', arguments);
            }
        });
    });
});
