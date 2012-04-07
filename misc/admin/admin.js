$(function () {

    // выделение активного раздела
    //var pathname = '/' + window.location.pathname.replace(/(^\/+)|(\/+$)/g, "") + '/';
    //$('a[href='+pathname+']').addClass('active');

    $(':button, :submit, :reset, .button').button();

    // Подсветка разделов структуры
    $('div.b-main-structure span').bind('mouseover',
        function () {
            $(this).addClass('active');
        }).bind('mouseout', function () {
            $(this).removeClass('active');
        });

    // Разделы
    $('a.link_del, a.do_delete').click(function () {
        return confirm('Данные будут потеряны. Действительно хотите удалить?');
    });

    $('a.order_hidden').live('click', function () {
        var a = this;
        $.get($(a).attr('href'), function (data) {
            $(a).replaceWith(data);
        });
        return false;
    });

    $('table.dataset tr').hover(function () {
        $(this).addClass('select');
    }, function () {
        $(this).removeClass('select');
    });

    $('a.link_add').click(function () {
        var page = $(this).attr('page'),
            url  = $(this).attr('href');
        if ( ! $('#link_add_dialog').length ) {
            $('body').append('<div id="link_add_dialog"></div>');
            $('#link_add_dialog').hide().dialog({
                'modal':true,
                'title':'Добавить связь',
                'buttons':{
                    'Отмена':function () {
                        $(this).dialog('close');
                    },
                    'Сохранить':function () {
                        $.post(url, {link_add:page});
                        $(this).dialog('close');
                    }
                },
                'autoOpen':false
            });
        }
        $.post(url, {get_link_add:page}, function (data) {
            $('#link_add_dialog').html(data).dialog('open');
        });
        return false;
    });

    // Галлерея
//    $('a.gallery, a.fancybox').lightBox();

    /**
     * Сортировка для структуры сайта
     */
    $('div.b-main-structure ul').sortable({
        stop:function (event, ui) {
            var positions = [];

            $(this).find('>li').each(function (i) {
                //positions[$(this).attr('this')] = i;
                positions.push($(this).attr('this'));
            });

            $.post('/admin/', {sort:positions}, function (data) {
                if (data.errno != 0) {
                    //alert(data.error);
                }
            }, 'json');
        }
    }).disableSelection();


    // Добавляем окно для обработчика форм
    if ( ! $('#form_container').length ) {

        $('body').append("<div id='form_container' title='Сохраняем...'></div>");
        //.append("<iframe id='form_frame' name='form_frame'></iframe>");

        $('#form_container').hide().dialog({
            bgiframe:true,
            modal:true,
            autoOpen:false,
            width:400,
            zindex:100,
            draggable:true,
            buttons:{
                Ok:function () {
                    $(this).dialog('close');
                },
                "Обновить":function () {
                    window.location.reload(true);
                }
            },
            close:function () {
                $(this).html("");
            }
        });
    }

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();

    $('a.realias').realias();

    /**
     * По 2х щелчку открыть менеджер файлов
     */
    $('input.image').dblclick($.fn.filemanager.input);

});

$.fn.realias = function () {
    return $(this).each(function () {
        $(this).click(function () {
            if ($("#realias_dialog").length == 0) {
                $('body').append("<div id='realias_dialog'></div>");
                $("#realias_dialog").dialog({
                    autoOpen:false,
                    width:650,
                    height:465,
                    modal:true,
                    resizable:false,
                    title:'Пересчет алиасов'
                });
            }
            $("#realias_dialog").html('<p>Ведется пересчет...<br />Не закрывайте окно.</p>').dialog("open");
            $.post($(this).attr('href'), function (request) {
                $("#realias_dialog").html(request);
            });
            return false;
        });
    });
}

$.fn.filemanager = function () {
    return $(this).click(function () {
        if ($("#filemanager_dialog").length == 0) {
            $('body').append("<div id='filemanager_dialog'></div>");
        }

        $("#filemanager_dialog").elfinder({
            "url":"/?controller=elfinder&action=connector",
            "lang":"ru",
            "dialog":$.fn.filemanager.dialog
        });
        return false;
    });
}

$.fn.filemanager.dialog = {
    width:650,
    height:465,
    title:"Файлы",
    modal:true,
    resizable:false
}

$.fn.filemanager.input = function () {

    var input = this;

    if ($("#filemanager_dialog").length == 0) {
        $('body').append("<div id='filemanager_dialog'></div>");
    }

    $("#filemanager_dialog").elfinder({
        "url":"/?controller=elfinder&action=connector",
        "lang":"ru",
        "dialog":$.fn.filemanager.dialog,
        "closeOnEditorCallback":true,
        "editorCallback":function (url) {
            $(input).val(url);
        }
    });
    return false;
}


$.fn.dumper = function () {
    return $(this).each(function () {
        var href    = $(this).attr("href");
        $(this).click(function () {
            if ($("#sfcms_dumper_dialog").length == 0) {
                $('body').append("<div id='sfcms_dumper_dialog'></div>");
                $("#sfcms_dumper_dialog").dialog({
                    autoOpen:false,
                    width:620,
                    height:510,
                    modal:true,
                    resizable:false,
                    title:'Архивация базы данных'
                }).append("<iframe src='"+href+"'></iframe>")
                    .find('iframe')
                    .css({
                        width:'590px', height:'465px', overflow:'hidden'
                    });
            }
            $("#sfcms_dumper_dialog").dialog("open");
            return false;
        });
    });
}




