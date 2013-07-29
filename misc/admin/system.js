/**
 * Модуль system
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("admin/system", [
    "jquery",
    "jquery/jquery.gallery"
], function($){
    return {
        "init" : function() {
            $('#tabs').tabs();
            $('a.assembly').gallery();
        }
    }
});
