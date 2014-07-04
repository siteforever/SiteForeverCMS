/**
 * Admin application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

require(["jquery", "system/admin"], function($){
    /** Placeholder while initialisation */
    $('#loading-application').each(function(){
        $(this).fadeOut(200, function(){
            $(this).remove();
        });
    });
});
