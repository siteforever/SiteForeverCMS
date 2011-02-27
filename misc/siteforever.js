/**
 * Фреймворк для взаимодействия с бакендом SiteForever CMS
 * Для работы требуется jQuery > 1.4
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

var siteforever = {};

/**
 * Логер
 * @param message
 */
siteforever.log = function( message )
{
    var message = message || '';
    if ( $('#siteforever_console').length ) {
        $('#siteforever_console').append('<div>'+message+'</div>');
    }
}

/**
 * Инициализация логгера
 */
siteforever.log.init    = function()
{
    $('body').append('<div id="siteforever_console"></div>');
}

/**
 * Произведет запрос
 * @param options
 */
siteforever.request = function( options )
{
    var url     = options['url']    || '/';
    var params  = options['params'] || {};
    var type    = options['type']   || 'json';

    $.post( url, params, function( response ) {
        if ( response.errno == 0 ) {
            if ( typeof options.success == 'function' ) {
                options.success( response );
            }
        }
        else {
            siteforever.log( response.error );
        }
    }, type);
}