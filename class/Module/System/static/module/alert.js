/**
 * Модуль включает инлайновый алерт
 * @author: keltanas
 * @link http://siteforever.ru
 */
define('system/module/alert',[
    "jquery",
    "jquery-blockui"
],function(){

    $.blockUI.defaults.css.border = 'none';
    $.blockUI.defaults.css.padding = '15px';
    $.blockUI.defaults.css['font-size'] = '16px';
    $.blockUI.defaults.css['border-radius'] = '10px';
    $.blockUI.defaults.css.color = '#fff';
    $.blockUI.defaults.css.backgroundColor = '#000000';
    $.blockUI.defaults.css.cursor = 'default';

    var alert = function( msg, timeout, node ) {
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

        if ( node ) {
            var $node = typeof node == 'string' ? $(node) : node,
                overflow = $node.css('overflow');
            $node.css('overflow','hidden').block(options);
            deferred.done(function(){
                $(node).css('overflow',overflow);
            });
        } else {
            $.blockUI( options );
        }

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
