/**
 * Модуль SiteForever
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("siteforever",[
    "jquery",
    "jui",
    "jquery/jquery.blockUI",
    "jquery/jquery.gallery",
    "jquery/jquery.captcha"
], function( $ ){

    console.log( 'SITEFOREVER:', arguments );

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

    $(document).ajaxStart(function(){
        $('<img src="/images/progress-bar.gif" alt="progress" id="progress">')
            .appendTo('body')
            .css({position:"absolute",right:20,top:50});
    }).ajaxStop(function(){
        $('#progress').remove();
    });

    var datepicker = {
        dateFormat:'dd.mm.yy',
        firstDay:1,
        changeMonth:true,
        changeYear:true,
        buttonImage:'/images/admin/icons/calendar.png',
        buttonImageOnly:true,
        showOn:'button',
        dayNamesMin:['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        monthNames:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
    };


    return {
        "alert" : alert,
        "datepicker" : datepicker
    };
});