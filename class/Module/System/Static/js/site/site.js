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
],function(
    $
  , basket
  , behavior
  , script
){
    $(document).ready($.proxy(function($){

        if (basket && basket.init && "function" == typeof basket.init) {
            basket.init();
        }

        if (script) {
            if (script.init && "function" == typeof script.init) {
                script.init();
            }
            behavior.apply(script);
        }

        $('a.gallery').gallery();
        $('span.captcha-reload').captcha();

        // добавить в корзину
        $(document).on('click', '.b-basket-add-button', function(e){
            e.stopPropagation();
            $(this).attr('disabled', 'disabled');
            var product = $(this).data('product'),
                properties = [];
            $( "input,select","#properties").each(function(){
                properties.push($(this).val());
            });
            basket.add(
                $(this).data('id'),
                product,
                $(this).siblings('input.b-basket-add-count').val(),
                $(this).data('price'),
                properties.join(", "),
                script && script.basket && typeof script.basket == 'function' ? script.basket : false
            ).done($.proxy(function () {
                $(this).removeAttr('disabled');
            }, this));
            return false;
        });
    },this));
});
