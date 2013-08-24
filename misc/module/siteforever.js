/**
 * Модуль SiteForever
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("module/siteforever",[
    "jquery"
], function($) {

//    $(document).ajaxStart(function(){
//        $('<img src="/images/progress-bar.gif" alt="progress" id="progress">')
//            .appendTo('body')
//            .css({position:"absolute",right:20,top:50});
//    }).ajaxStop(function(){
//        $('#progress').remove();
//    });

    window.datepicker = {
        dateFormat:'dd.mm.yy',
        firstDay:1,
        changeMonth:true,
        changeYear:true,
//        buttonImage:'/images/admin/icons/calendar.png',
        buttonImageOnly:true,
        showOn:'button',
        dayNamesMin:['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        monthNames:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
    };
});
