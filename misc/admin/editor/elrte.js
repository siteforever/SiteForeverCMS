/**
 * Wysiwyg editor elrte
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define([
    'jquery',
    'jui',
    'elrte/js/elrte.full',
    'elrte/js/i18n/elrte.ru'
], function($){
    $('head').append('<link type="text/css" rel="stylesheet" href="/misc/elrte/css/elrte.min.css">');
    return {
        'init' : function() {
            $('textarea').not('.plain').elrte( {
                lang : 'ru',
                styleWithCSS : false,
                height : 200,
                toolbar : 'maxy'
            } );
        }
    };
});