$(function() {

    // выделение активного раздела
    //var pathname = '/' + window.location.pathname.replace(/(^\/+)|(\/+$)/g, "") + '/';
    //$('a[href='+pathname+']').addClass('active');

    $(':button, :submit, :reset, .button').button();

    // Подсветка разделов структуры
    $('div.b-main-structure span').bind('mouseover', function() {
        $(this).addClass('active');
    }).bind('mouseout', function() {
        $(this).removeClass('active');
    });

    // Разделы
    $('a.link_del').click(function(){
        return false;
        var page = $(this).attr('page');
        var url  = $(this).attr('href');
        $.post(url, {link_del:page}, function(data){
            alert(data);
        });
        return false;
    });

    $('a.do_delete').click(function(){
        return confirm('Данные будут потеряны. Действительно хотите удалить?');
    });

    $('table.dataset tr').hover(function(){
        $(this).addClass('select');
    },function(){
        $(this).removeClass('select');
    });


    $('a.link_add').click(function() {
        if ( $('#link_add_dialog').length == 0 ) {
            $('body').append('<div id="link_add_dialog"></div>');
            $('#link_add_dialog').hide().dialog({
                'modal':       true,
                'title':       'Добавить связь',
                'buttons':     {
                    'Отмена': function () {
                       $(this).dialog('close');
                    },
                    'Сохранить': function() {
                        $.post(url, {link_add:page}, function(data){
                            //alert(data);
                        });
                        $(this).dialog('close');
                    }
                },
                'autoOpen':    false
            });
        }


        var page = $(this).attr('page');
        var url  = $(this).attr('href');
        $.post(url, {get_link_add:page}, function(data){
            $('#link_add_dialog').html(data).dialog('open');
        });
        return false;
    });

    // Галлерея
    $('a.gallery, a.fancybox').fancybox();

    $('div.b-main-structure ul').sortable({
        stop:
            function( event, ui )
            {
                var positions = [];

                $(this).find('>li').each(function(i) {
                    //positions[$(this).attr('this')] = i;
                    positions.push( $(this).attr('this') );
                });

                $.post('/admin/', {sort:positions}, function(data){
                    if ( data.errno != 0 ) {
                        //alert(data.error);
                    }
                }, 'json');
            }
    }).disableSelection();


    // Добавляем окно для обработчика форм
    if ( $('#form_container').length == 0 ) {

        $('body').append("<div id='form_container' title='Сохраняем...'></div>");
        //.append("<iframe id='form_frame' name='form_frame'></iframe>");

        $('#form_container').hide().dialog({
            bgiframe: true,
            modal: true,
            autoOpen: false,
            width: 400,
            zindex: 100,
            draggable: true,
            buttons: {
                Ok: function() {
                    $(this).dialog('close');
                },
                "Обновить": function() {
                    window.location.reload(true);
                }
            },
            close: function() {
                $(this).html("");
            }
        });
    }




    //$('a.filemanager').filemanager();
    $('a.filemanager').fancybox({
        width       : 650,
        height      : 430,
        scrolling   : 'no',
        type        : 'iframe',
        href        : '/?controller=elfinder&finder=1&langCode=ru'
    });

   /* var editor = $('textarea').not('.plain').ckeditor({
        filebrowserBrowseUrl 		: '/?controller=elfinder&action=index&finder=1',
        filebrowserImageBrowseUrl 	: '/?controller=elfinder&action=index&finder=1',
        filebrowserWindowWidth : '530',
        filebrowserWindowHeight : '500',
        filebrowserImageWindowWidth : '530',
        filebrowserImageWindowHeight : '500'
    });*/

    $('textarea').not('.plain').tinymce({
        // Location of TinyMCE script
        script_url :    '/misc/tinymce/jscripts/tiny_mce/tiny_mce.js',
        // General options
        theme :         'advanced',
        language:       'ru',
        convert_urls :  false,
        // Theme options
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        // Connect ElFinder
        file_browser_callback : function(field_name, url, type, win) {
            var w = window.open('/?controller=elfinder&action=index&finder=1', null, 'width=600,height=420');
            // Сохраняем необходимые параметры в глобальных переменных окна (не самое лучшее решение, предложите другое?),
            // или можете передавать параметры в GET и потом разбирать их в elfinder.html
            w.tinymceFileField = field_name;
            w.tinymceFileWin = win;
        }
    });

    $('a.dumper').dumper();

});

$.fn.dumper = function()
{
    return $(this).each(function(){
        $(this).click(function(){
            if ( $("#dumper_dialog").length == 0 ) {
                 $('body').append("<div id='dumper_dialog'></div>");
                 $("#dumper_dialog").dialog({
                     autoOpen:  false,
                     width:     620,
                     height:    510,
                     modal:     true,
                     resizable: false,
                     title:     'Архивация базы данных'
                 }).append("<iframe src='/misc/sxd'></iframe>")
                         .find('iframe')
                         .css({
                            width: '590px', height: '465px', overflow: 'hidden'
                           });
             }
             $("#dumper_dialog").dialog("open");
            return false;
        });
    });
}




