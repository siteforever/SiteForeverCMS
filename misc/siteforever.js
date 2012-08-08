/**
 * Фреймворк для взаимодействия с бакендом SiteForever CMS
 * Для работы требуется jQuery > 1.4
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

var siteforever = function(){},
    sf = siteforever,
    $s = siteforever,
    sfcms = siteforever;


siteforever.alert   = function( msg, timeout )
{
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

siteforever.alert.close = function( timeout )
{
    timeout = timeout || 0;
    if (timeout) {
        setTimeout($.unblockUI, timeout);
    } else {
        $.unblockUI();
    }
};

$.fn.gallery = function() {
    $(this).each(function(){
        $(this).fancybox({titlePosition:'inside'});
    });
};

$.fn.captcha = function() {
    $(this ).each(function(){
        $(this).click(function(){
            var img = $(this).parent().find('img');
            $(img).attr('src', $(img).attr('src').replace(/\&hash=[^\&]+/, '')+'&hash='+Math.random());
        });
    });
};

$(function(){
    function initBlockUI() {
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
    }
    if ( $.blockUI ) {
        initBlockUI();
    } else {
        $.getScript('/misc/jquery/jquery.blockUI.js', initBlockUI);
    }

    $( '.siteforever_captcha_reload' ).captcha();

    $(document).ajaxStart(function(){
        $('<img src="/images/progress-bar.gif" alt="progress" id="progress">')
            .appendTo('body')
            .css({position:"absolute",right:20,top:50});
    }).ajaxStop(function(){
        $('#progress').remove();
    });
});