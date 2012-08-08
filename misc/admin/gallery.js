/**
 * Управление админкой галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
siteforever.gallery = {
    /**
     * Редактирование имени
     */
    nameEdit: function( event ){

//        console.log( 'click div | ', 'ev.target: ', event.target, ' this:', this );

        event.stopPropagation();

        var self = this,
            val  = $(this).find('input').val(),
            name = $(this).find('input').attr('name');
        $(this).find('span').hide().next().hide();

        $("<input type='text' name='"+name+"' value='"+val+"' data-old='"+val+"' />").prependTo(this).focus();

        $(this).find('input:text')
            .blur(function(event){
                sf.gallery.nameApply.call( self );
//                console.log( 'blur:text | ', event.target, ' this:', this );
            })
            .click(function(event){
//                event.stopPropagation();
//                console.log( 'click:text | ', event.target, ' this:', this );
                return false;
            })
            .keypress(function( event ){
                if (event.keyCode == '13') {
                    sf.gallery.nameApply.call( self );
                }
                if (event.keyCode == '27') {
                    sf.gallery.nameCancel.call( self );
                }
            });
        return false;
    },

    /**
     * Редактировать название и применить
     */
    nameApply: function () {
        var text = $( this ).find( ':text' ),
            val = $( text ).val(),
            old = $( text ).attr( 'data-old' ),
            id = $( this ).attr( 'rel' );

        if ( id && val != old ) {
            $.post( '/?route=gallery/admin', { editimage: id, name: val } );
        }
        $( this ).find( 'span' ).text( val ).show().next().show().next().val( val );
        $( text ).remove();
    },

    /**
     * Редактировать название и отменить
     */
    nameCancel: function () {
        var text = $( this ).find( ':text' ),
            val = $( text ).attr( 'data-old' );
        $( this ).find( 'span' ).text( val ).show().next().show();
        $( text ).remove();
    },

    /**
     * Конфиг диалога редактирования
     */
    editDialog: {
        autoOpen        : false,
        modal           : true,
        width           : 700,
        position        : 'center',
        title           : 'Правка информации',
        open            : function() {
            wysiwyg.init();
            $( '#tabs' ).tabs();
        },
        buttons         : [
            {
                text: sf.i18n('Save'),
                click : function() {
                    $(this).find('form').ajaxSubmit({
                        success : function(response) {
                            sf.alert(response, 2000);
                            return true;
                        },
                        error: function () {
                            sf.alert('Данные не сохранены',2000);
                            return true;
                        }
                        //target  : '#gallery_picture_edit'
                    });
                    $(this).dialog('close');
                    sf.alert('Отправка...');
                    return true;
                }
            },
            {
                text: sf.i18n('Cancel'),
                click : function() {
                    $(this).dialog('close');
                }
            }
        ]
    }

};


$(function() {
    // Сортировочность
    $("#gallery").sortable({
        stop: function(event, ui) {
            var positions = [];
            $(this).find('li').each(function(){
                positions.push($(this).attr('rel'));
            });
            $.post('/?route=gallery/admin', { positions: positions });
        }
    });
    $("#gallery").disableSelection();

    // Редактирование названия
    $('#gallery').find('div.gallery_name').click(sf.gallery.nameEdit);


    // Правка данных об изображении
    $('a.gallery_picture_edit').each(function(){
        $(this).click(function(){
            action = $(this).attr('href');
            if ( 0 == $('#gallery_picture_edit').length ) {
                $('<div id="gallery_picture_edit" />').appendTo('div.l-content-wrapper').dialog( sf.gallery.editDialog ).hide();
            }

            $(window).bind('close', function(){ return false; });

            $.post($(this).attr('href'), $.proxy( function( action, response ){
//                console.log( arguments );
                $('#gallery_picture_edit')
                    .html( response )
                    .dialog('open')
                    .find('form').attr('action', action);
                return true;
            }, this, action ));

            return false;
        });
    });


    // Удаление изображений
    $('a.gallery_picture_delete').click(function(){
        if ( confirm('Действительно хотите удалить?') ) {
            var href = $(this).attr('href');
            $.post( href, function(data) {
                try {
                    if ( data.errno == '0' ) {
                        var elem = $('#gallery').find('li[rel='+data.id+']');
                        $(elem).fadeOut(500);
                        setTimeout(function(){
                            $(elem).remove();
                        }, 1000);
                    }
                } catch(e) { alert(e.message) };
            }, 'json');
        }
        return false;
    });

    // Переключение активности изображения
    $('a.gallery_picture_switch').click(function(){
        $.post($(this).attr('href'), function(data){
            try {
                if ( data.errno == '0' ) {
                    var elem = $('#gallery li[rel='+data.id+'] a.gallery_picture_switch' );
                    $(elem).html(data.img);
                }
                else {
                    alert( data.error );
                }
            } catch(e) { alert(e.message) };
        }, 'json');
        return false;
    });

    // Создание мультизагрузки
    var reserv_img = $("div.newimage:last").clone();
    $("#add_image").click(function(){
        $(reserv_img).clone().appendTo("#load_images");
        return false;
    });
    $("#send_images").click(function(){
        $("#load_images").submit();
        return false;
    });
});
