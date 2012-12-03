/**
 * SiteRuns application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
require([
      "jquery"
    , "module/basket"
    , "module/behavior"
    , "theme/js/script"
    , "jquery/jquery.gallery"
    , "jquery/jquery.captcha"
    , "siteforever"
    , "twitter"
],function(
    $
  , basket
  , behavior
  , script
){
    $(document).ready($.proxy(function($){

        if ( basket && basket.init && "function" == typeof basket.init) {
            basket.init();
        }

        if ( script ) {
            if ( script.init && "function" == typeof script.init ) {
                script.init();
            }
            behavior.apply( script );
        }

        $('a.gallery').gallery();
        $('span.captcha-reload').captcha();

        // добавить в корзину
        $(document).on('click', 'input.basket-add', function(){
            var product = $(this).data('product'),
                properties = [];
            $( "input,select","#properties").each(function(){
                properties.push( $(this).val() );
            });
            basket.add(
                $(this).data('id'),
                product,
                $(this).parent().find('input.b-product-basket-count').val(),
                $(this).data('price'),
                properties.join(", "),
                script.basket && typeof script.basket == 'function' ? script.basket : false
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
            if ( $(this).val() <= 0 ) $(this).val(1);
            $('#recalculate').trigger('click');

        });
    },this));
});