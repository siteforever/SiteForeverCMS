/**
 * Дополнительные скрипты для каталога
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

$.ready(function(){
    /*
     * Управляет галереей картинок в товаре каталога
     */
    $('ul.b-product-gallery a:has(img)').fancybox();
    $('div.b-product-image a').fancybox();
    $('a.gallery').fancybox();


    // Управление сортировкой каталога
    $('select.catalog_select_order').bind('change', function() {
        var href = window.location.href;
        href = href.replace(/\/$/, '').replace(/(\/order=[\w]+)*$/, '');
        if ( $(this).val() != '' ) {
            href += '/' + 'order=' + $(this).val();
        }
        window.location.href = href;
    });    
});

/**
 * Схлопывает и расхлопывает меню каталога
 */
$.fn.catalog_collapse = function() {
    $('ul', this).find('ul').hide();
    $('li:has(li.active)', this).addClass('active');
    $('li.active>ul', this).show();
    $('li', this).has('li').not('.active').addClass('minus');
    $('li.active', this).has('li').addClass('plus');
    $('li', this).has('ul').find('a').click(function(e){
        if (e.target == this) {
            var li   = this;
            var tag  = e.target.tagName;
            if ( tag == 'A' ) {
                li   = $(this).parent();
            }
            var ul = $(li).find('ul').first();
            if ( $(ul).css('display') == 'none' ) {
                $(ul).show(300);
                if ( $(ul).length ) $(li).addClass('plus').removeClass('minus');
            } else {
                $(ul).hide(300);
                if ( $(ul).length ) $(li).removeClass('plus').addClass('minus');
            }
            if ( tag == 'A' && $(ul).length > 0 ) {
                return false;
            }
        }
    });
}

/**
 * Делает подпункты каталога "всплывающими" при наведении
 */
$.fn.catalog_slider = function()
{
    // флаги индикации
    $.fn.catalog_slider.move_li = 0;
    $.fn.catalog_slider.move_ul = 0;

    // инициализация
    $('a', this).css('display','block');

    // добавляем значек к открывающимся пунктам
    $('li:has(ul)', this).find('a:first').append(' <b>&raquo;</b>');

    // применяем нужные стили к спискам
    $('ul:gt(0)',this).css({
        position    : 'absolute',
        background  : '#fff',
        border      : '1px solid #900',
        width       : 210+'px',
        visibility  : 'hidden',
        padding     : '0 5px 5px 0'
    });

    $(this).find('li:has(ul)').each(function(){
        var o = $(this).offset();
        $('ul:first',this).offset({
            top     : o.top,
            left    : o.left + 190
        });
    });

    // все пункты, содержащие списки
    $(this).find('li:has(ul)').hover(function(e) {
        // при наведении
        var offset  = $(this).offset();
        $.fn.catalog_slider.move_li = 1;

        $('ul:first',this).offset({
            top     : offset.top,
            left    : offset.left + 190
        }).css('visibility','visible').hover(function(){
            $.fn.catalog_slider.move_ul = 1;
        }, function(){
            $.fn.catalog_slider.move_ul = 0;
            $(this).css('visibility','hidden');
        });

    },function(e) {
        // при убирании
        $.fn.catalog_slider.move_li = 0;
        $(this).find('ul:first').css('visibility','hidden');
    });

    return this;
};