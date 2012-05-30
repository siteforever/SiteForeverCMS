/**
 * Скрипты для каталога
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
$(function(){
    if ( $('table.catalog_data').length ) {
        $('table.catalog_data').sortable({
            items           : 'tr[rel]',
            forceHelperSize : true,
            appendTo        : 'body',
            forcePlaceholderSize: true,
            axis            : 'y',
            update :
                function( event, ui ) {
                    var positions = [];
                    var total = $(this).find('tr[rel]').length;
                    $(this).find('tr[rel]').each(function(i) {
                        positions.push( $(this).attr('rel') );
                    });
                    positions.reverse();
                    $.post('/catalog/saveorder', {sort:positions}, function( data ){
                        try {
                            if ( data.errno ) alert( data.error );
                        } catch (e) {};
                    }, 'json');
                }
        });
        $('table.catalog_data tr.cat').disableSelection();



        $('#catalog_save_position').click(function(){

            siteforever.alert('Сохранение...');
            var pos = [];
            $('input.trade_pos').each(function(){
                pos.push({key:$(this).attr('rel'), val:$(this).val()});
            });
            $.post($(this).attr('href'), { "save_pos": pos }, function( data ){
//                siteforever.alert.close();
                document.location.reload();
            });
            //alert(pos);
        });



        $('#catalog_move_to_category').click(function(){
            var move_list = [];
            $('table.catalog_data input:checked').each(function(i){
                move_list.push($(this).val());
            });
            var target = $('#catalog_move_target').val();
            if ( move_list.length ) {
                $.post($(this).attr('href'), {move_list:move_list, target:target}, function(data){
                    try {
                        if (! data.errno) {
                            document.location.reload();
                        } else {
                            alert(data.error);
                        }
                    } catch(e) {
                        alert(e.message);
                    };
                }, 'json');
            }
            //alert( move_list.join(',') );
            return false;
        });


        $('#catalog_move_target').css({width:300+'px'});

        $('a.catalog_switch').live('click', function(){
            var a_obj = $(this);
            $.post($(this).attr('href'), function(data){
                if ( data.error == '0' ) {
                    $(a_obj).attr('href',data.href).html(data.img);
                }
            }, 'json');
            return false;
        });
    }


    // Фильтрация товаров
    $('#goods_filter_select').click(function(){
        var href = window.location.href;
        href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
        if ( $('#goods_filter').val() != '' ) {
            href += '/' + 'goods_filter=' + $('#goods_filter').val();
        }
        window.location.href = href;
    });
    // Отмена фильтрации
    $('#goods_filter_cancel').click(function(){
        var href = window.location.href;
        href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
        window.location.href = href;
    });


    // Галерея каталога
    if ( $('a.gallery-item-add').length ) {

        $('a.gallery-item-add').live('click',function(){
            if ( $('#gallery_dialog').length == 0 ) {
                $('body').append('<div id="gallery_dialog"/>');
                $('#gallery_dialog').dialog({
                    autoOpen    : false,
                    modal       : true,
                    title       : "Добавить изображения",
                    buttons     : {
                        "Закрыть"   : function() {
                            $(this).dialog('close');
                        },
                        "Загрузить" : function() {
                            $(this).find('form').ajaxSubmit({
                                target  : '#gallery_dialog'
                            });
                        }
                    },
                    close: function() {
                        $.get('/catgallery/index/id/'+$('#catalog_id').val(), function(data) {
                            $('div.a-gallery').replaceWith(data);
                        });
                    }
                });
            };
            $('#gallery_dialog').html('Загрузка...').dialog('open');
            $.get( $(this).attr('href'), function( data ) {
                $('#gallery_dialog').html(data);
            });
            return false;
        });

        // удалить изображение
        $('a.del_gallery_image').live('click', function(){
            if ( ! confirm('Действительно хотите удалить изображение?') ) {
                return false;
            }
            siteforever.alert('Удаление');
            $.get($(this).attr('href'), function(data){
                $('div.a-gallery:first').replaceWith(data);
                siteforever.alert.close();
            });
            return false;
        });

        // сделать изображение главным
        $('a.main_gallery_image').live('click', function(){
            siteforever.alert( 'Сохранение', null );
            $.get($(this).attr('href'), function(data){
                $('div.a-gallery:first').replaceWith(data);
                siteforever.alert.close();
            });
            return false;
        });
    }

});