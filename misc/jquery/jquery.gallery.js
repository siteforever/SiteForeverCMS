/**
 * Gallery
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
$.fn.gallery = function() {
     $(this).each(function(){
         $(this).fancybox({titlePosition:'inside'});
     });
 };