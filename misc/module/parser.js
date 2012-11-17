/**
 * Парсер страницы, ищущий декларативно объявленные модули
 */
define('module/parser',function(require){
    return function() {
        $('[data-sfcms-module]').each($.proxy(function(_,domNode){
            if ( 'jquery/jquery.jqGrid' == $(domNode).data('sfcms-module') ) {
                require( 'jquery/jquery.jqGrid' );
            }
        },this));
    }
});