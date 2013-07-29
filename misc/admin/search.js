/**
 * Модуль поиска
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("admin/search", [
    "jquery",
    "jui"
], function($){
    return {
        "behavior" : {
            "#indexing" : {
                "click" : function( event, node ) {
                    $('#searchLog').html( 'Обработка...' );
                    $.get( $(node).attr('href') ).done(function( response ){
                         $('#searchLog').html( response );
                    });
                    return false;
                }
            }
        }
    };
});
