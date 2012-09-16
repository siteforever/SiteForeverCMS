/**
 * Captcha
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
$.fn.captcha = function() {
    $( this ).each(function(){
        $(this).click(function(){
            var img = $(this).parent().find('img');
            $(img).attr('src', $(img).attr('src').replace(/\&hash=[^\&]+/, '')+'&hash='+Math.random());
        });
    });
};
