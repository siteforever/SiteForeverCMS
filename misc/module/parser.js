/**
 * Парсер страницы, ищущий декларативно объявленные модули
 */
define('module/parser',function(require){
    return function() {
        require('jquery/jquery.jqGrid');
        $('[data-sfcms-module]').each($.proxy(function(_,domNode){
            var moduleName   = $(domNode).data('sfcms-module');
            require( moduleName );
        },this));
    }
});