/**
 * Gallery
 *
 * Dependensis will be in shim
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("system/jquery/jquery.gallery", [
    "jquery",
    "fancybox"
], function($){
    $.fn.gallery = function() {
        return $(this).each(function(){
            $(this).fancybox({titlePosition:'inside'});
        });
    };
});
