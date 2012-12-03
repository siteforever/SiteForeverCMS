/**
 * Модуль включает инлайновый алерт
 * @author: keltanas
 * @link http://siteforever.ru
 */
define('module/alert',[
    "jquery",
    "jquery/jquery.blockUI"
],function(){

    $.blockUI.defaults.css.border = 'none';
    $.blockUI.defaults.css.padding = '15px';
    $.blockUI.defaults.css['font-size'] = '16px';
    $.blockUI.defaults.css['border-radius'] = '10px';
    $.blockUI.defaults.css.color = '#fff';
    $.blockUI.defaults.css.backgroundColor = '#000';
    $.blockUI.defaults.css.cursor = 'default';
    $.blockUI.defaults.overlayCSS.backgroundColor = '#000';
    $.blockUI.defaults.overlayCSS.opacity = 0.4;
    $.blockUI.defaults.overlayCSS.cursor = 'default';

    var alert = function( msg, timeout ) {
        timeout = timeout || 0;
        var deferred = $.Deferred();

        var options = {
            message: '<div>'+msg+'</div>'
        };

        if ( timeout ) {
            options.timeout = timeout;
            options.onUnblock = function() {
                deferred.resolve();
            }
        } else {
            deferred.resolve();
        }

        $.blockUI( options );

        return deferred.promise();
    };

    alert.close = function( timeout )
    {
        timeout = timeout || 0;
        if (timeout) {
            setTimeout($.unblockUI, timeout);
        } else {
            $.unblockUI();
        }
    };

    return alert;
});