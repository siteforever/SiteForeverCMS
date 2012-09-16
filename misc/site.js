/**
 * SiteRuns application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
require([
    "jquery"
  , "siteforever"
  , "etc/basket"
  , "theme/js/script"
  , "etc/catalog"
  , "jquery/jquery.gallery"
  , "jquery/jquery.captcha"
  , "twitter"
],function(
    $
  , $s
  , basket
  , script
){
    if ( script && script.init && "function" == typeof script.init) {
        script.init();
    }

    if ( basket && basket.init && "function" == typeof basket.init) {
        basket.init();
    }


    $('a.gallery').gallery();
    $('span.captcha-reload').captcha();

    // добавить в корзину
    $(document).on('click', 'input.basket-add', function(){
        var product = $(this).data('product');
        basket.add(
            $(this).data('id') || product,
            product,
            $(this).parent().find('input.b-product-basket-count').val(),
            $(this).data('price'),
            ''
        );
    });


    // Обработка выбора доставки
    $(document).on('click','#delivery input', function(){
        $.post('/delivery/select',{'type':$(this).val()},function(response){
            if( ! response.error ) {
                $('#deliveryRow td.basket-sum').html( response.cost );
                $('#totalRow td.basket-sum').find('b').html( response.sum );
            }
        }, "json");
    });

    $('input.basket-count').on('change', function(){
        $('#recalculate').trigger('click');
    });

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
                                ? $(divControls).append(divMsg)
                                : $(this).append(divMsg);
                        }
                    } else {
                        $(this).removeClass('error').find('div.error').remove();
                    }
                });
            }

            if ( response.redirect ) {
                window.location.href = response.redirect;
            }

            if ( response.basket ) {
                for ( i in response.basket ) {
                    if ( parseInt( i ) > 0 ) {
                        item = response.basket[i];
                        $('tr[data-id='+item.id+']').find('.basket-sum')
                            .html( ( parseFloat(item.count) * parseFloat(item.price) ).toFixed(2).replace('.',',') );
                    }
                }
                if ( response.basket.delitems ) {
                    for( i in response.basket.delitems ) {
                        $('tr[data-id='+response.basket.delitems[i]+']').remove();
                    }
                }
                $('#totalRow').find('.basket-count').find('b').html( response.basket.count );
                $('#totalRow').find('.basket-sum').find('b').html( (response.basket.sum).toFixed(2).replace('.',',') );
            }

            if ( script.formResponse && typeof script.formResponse == 'function' ) {
                script.formResponse( response );
            }
        }
    });
});