/**
 *    @author Nikolay Ermin
 *
 *    JavaScript приложение к модулю форм
 */
$(function () {
    // вводим uri
    $('#structure_uri,#structure_alias').bind('keypress', function (event) {
        if (event.keyCode == 8 || event.keyCode == 9 ||
            ( event.keyCode >= 33 && event.keyCode <= 40 ) ||
            ( event.keyCode >= 45 && event.keyCode <= 47 ) ||
            ( event.charCode >= 47 && event.charCode <= 57 ) ||
            ( event.charCode >= 95 && event.charCode <= 122 && event.charCode != 96 )
            ) {
        }
        else {
            event.preventDefault();
            return false;
        }
    });

    // обработчик сабмита

    $('form.module_form, form.ajax').ajaxForm({
        beforeSubmit:function () {
            $.showBlock('Отправка данных...');
        },
        success:function (data) {
            $('div.blockMsg').html(data);
            $.hideBlock(2000);
        },
        iframe:false
    }).find("input:text").live('keypress', function (e) {
            if (e.keyCode == 13 /*|| e.keyCode == 9*/) {
                return false;
            }
        });


    /*
     * Цепляем элементы календаря
     */
    if (typeof window[$.datepicker] == 'function') {
        $('.datepicker').datepicker({
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
        });
    }


    $(':reset').click(function () {
        $(this).parents("form").clearForm().submit();
        return false;
    });

});


// отображает блокировку
$.showBlock = function (message) {
    $.blockUI({
        message:message,
        css:{
            border:'none',
            padding:'15px',
            'font-size':'16px',
            backgroundColor:'#000',
            '-webkit-border-radius':'10px',
            '-moz-border-radius':'10px',
            'border-radius':'10px',
            color:'#fff'
        }
    });
}

// скрывает блокировку
$.hideBlock = function (timeout) {
    timeout = timeout || 0;
    if (timeout) {
        setTimeout($.unblockUI, timeout);
    } else {
        $.unblockUI();
    }
}

/**
 *    Функция создает из строки ?a=1&b=2 массив {"a":"1","b":"2"}
 */
var requestSplit = function (request) {
    var data = {};
    for (var val in request.replace('?', '').split('&')) {
        var a = val.split('=');
        data[ a[0] ] = a[1];
    }
    return data;
}