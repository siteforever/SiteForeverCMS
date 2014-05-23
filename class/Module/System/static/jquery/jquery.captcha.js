/**
 * Captcha
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("system/jquery/jquery.captcha", [
    "jquery"
], function($){
    $.fn.captcha = function() {
        $( this ).each(function(){
            $(this).click(function(){
                var $img = $(this).parent().find('img'),
                    src = $img.attr('src'),
                    delim = '?',
                    pattern;
                pattern = new RegExp('[\\?\\&]hash=.*');
                src = src.replace(pattern, '');
                if (src.match(new RegExp('/[\\?\\&]/'))) {
                    delim = '&';
                }
                $img.attr('src', src + delim + 'hash=' + Math.random());
            });
        });
    };
});
