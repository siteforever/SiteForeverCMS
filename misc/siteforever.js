/**
 * Фреймворк для взаимодействия с бакендом SiteForever CMS
 * Для работы требуется jQuery > 1.4
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

var siteforever = {},
    sf = siteforever;

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
    $('#siteforever_console').hide();
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

siteforever.alert   = function( msg, timeout )
{
    if ( null !== timeout ) {
        if ( ! timeout )
            timeout = 2000;
    }

    if ( $('#siteforever_alert').length == 0 ) {
        $('body').append('<div id="siteforever_alert" style="display:none;"/>');
        $('#siteforever_alert').css({
            background: '#666',
            color:      'white',
            padding:    '10px',
            'border-radius': '10px',
            '-moz-border-radius': '10px',
            '-webkit-border-radius': '10px',
            position:   'absolute'
        });
    }

    if ( null !== timeout && siteforever.alert.timeout ) {
        clearTimeout( siteforever.alert.timeout );
        siteforever.alert.close();
    }

    //alert($(window).scrollTop());

    $('#siteforever_alert').html( msg ).css({
        top:    Math.round( $(window).height() / 2 - $('#siteforever_alert').height() / 2 + $(window).scrollTop() ),
        left:   Math.round( $(window).width() / 2 - $('#siteforever_alert').width() / 2 )
    }).fadeTo( 'slow', 0.8 );

    if ( null !== timeout ) {
        siteforever.alert.timeout = setTimeout(function(){
            siteforever.alert.close();
        }, timeout);
    }
};

siteforever.alert.close = function()
{
    $('#siteforever_alert').hide();
}




$(function(){
    if ( $('.siteforever_captcha_reload').length > 0 ) {
        $('.siteforever_captcha_reload').click(function(){
            var img = $(this).parent().find('img');
            $(img).attr('src', $(img).attr('src').replace(/\&hash=[^\&]+/, '')+'&hash='+Math.random());
        });
    }
})