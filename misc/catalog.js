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
                    $.post('/admin/catalog', {sort:positions}, function( data ){
                        try {
                            if ( data.error ) alert( data.error );
                        } catch (e) {};
                    }, 'json');
                }
        });
        $('table.catalog_data tr.cat').disableSelection();

        $('#catalog_save_position').click(function(){
            var pos = [];
            $('input.trade_pos').each(function(){
                pos.push({key:$(this).attr('rel'), val:$(this).val()});
            });
            $.post($('input.trade_pos:gt(0)').attr('href'), { save_pos: pos }, function(){
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
                $.post('/admin/catalog', {move_list:move_list, target:target}, function(data){
                    try {
                        if (data.error == '') {
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


    // Галлерея каталога
    if ( $('a.gallery-item-add').length ) {
        $('a.gallery-item-add').live('click',function(){
            if ( $('#gallery_dialog').length == 0 ) {
                $('body').append('<div id="gallery_dialog"></div>');
                $('#gallery_dialog').dialog({
                    autoOpen    : false,
                    modal       : true,
                    title       : "Добавить изображения",
                    buttons     : {
                        "Закрыть"   : function() {
                            if ( $(this).find('form').length == 0 ) {
                                $.get('/admin/catgallery/reload='+$('#form_catalog_id').val(), function(data) {
                                    $('div.a-gallery:first').replaceWith(data);
                                });
                            };
                            $(this).dialog('close');
                        },
                        "Загрузить" : function() {
                            $(this).find('form').ajaxSubmit({
                                target  : '#gallery_dialog'
                            });
                        }
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
            if ( confirm('Действительно хотите удалить изображение?') ) {
                $.blockUI({
                    message:'Удаление',
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }
                });
                $.get($(this).attr('href'), function(data){
                    $('div.a-gallery:first').replaceWith(data);
                    $.unblockUI();
                });
            }
            return false;
        });

        // сделать изображение главным
        $('a.main_gallery_image').live('click', function(){
            $.blockUI({
                message:'Сохранение',
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });
            $.get($(this).attr('href'), function(data){
                $('div.a-gallery:first').replaceWith(data);
                $.unblockUI();
            });
            return false;
        });
    }

})