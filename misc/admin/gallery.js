/**
 * Управление админкой галереи
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
define([
    "jquery",
    "siteforever",
    "wysiwyg",
    "module/modal",
    "i18n",
    "jui"
], function($, $s, wysiwyg, Modal){
    return {
        "init" : function() {

            var self = this;

            // Сортировка
            $("#gallery").sortable({
                stop: function(event, ui) {
                    var positions = [];
                    $(this).find('li').each(function(){
                        positions.push($(this).attr('rel'));
                    });
                    $.post('/?route=gallery/admin', { positions: positions });
                }
            }).disableSelection();

            // Редактирование названия
            $('#gallery').find('div.gallery_name').each( function(){
                $(this).click($.proxy( self.nameEdit, self ) );
            });

            $('a.do_delete').each(function(){
                $(this).on('click', function(){return confirm($s.i18n('Want to delete?'));});
            });

            if ( $('#editImage').length ) {
                var editImage = new Modal('editImage');
                editImage.onSave(this.onSave);
                // Правка данных об изображении
                $('a.gallery_picture_edit').each(function(){
                    $(this).click(function(){
                        action = $(this).attr('href');
                        $.post($(this).attr('href'), $.proxy( function( action, response ){
                            editImage.title('Правка информации').body( response).show();
                            return true;
                        }, this, action ));
                        return false;
                    });
                });
            }


            // Удаление изображений
            $('a.gallery_picture_delete').each(function(){
                $(this).click(function(){
                    if ( confirm('Действительно хотите удалить?') ) {
                        var href = $(this).attr('href');
                        $.post( href, function(data) {
                            try {
                                if ( data.error == '0' ) {
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
            });

            // Переключение активности изображения
            $('a.gallery_picture_switch').each(function(){
                $(this).click(function(){
                    $.post($(this).attr('href'), function(response){
                        try {
                            if ( response.error == '0' ) {
                                $('#gallery li[rel='+response.id+'] a.gallery_picture_switch' ).html(response.img);
                            } else {
                                $s.alert( response.msg );
                            }
                        } catch(e) { $s.alert(e.message) }
                    }, 'json');
                    return false;
                });
            });

            // Создание мультизагрузки
            var reservImg = $("div.newimage:last").clone();
            $("#add_image").click(function(){
                $(reservImg).clone().appendTo("#load_images");
                return false;
            });
            $("#send_images").click(function(){
                $("#load_images").submit();
                return false;
            });


            /**
             * Управление списком галерей
             */
            if ( $('#editCat').length ) {
                var editCat = new Modal('editCat');
                editCat.onSave(this.onSaveCat);
                $('a.editCat').each(function(){
                    $(this).on('click', function(){
                        $.get( $(this).attr('href'), $.proxy( function( editCat, response ) {
                            console.log( this, editCat );
                            editCat.title( $(this).attr('title') ).body( response ).show();
                        }, this, editCat ));
                        return false;
                    });
                });
            }
        },

        /**
         * Редактирование имени
         */
        nameEdit: function( event ){
            event.stopPropagation();
//            console.log( 'click div ', 'event: ', event, ' this:', this );
            var domNode = event.currentTarget;
            var val  = $(domNode).find('input').val(),
                name = $(domNode).find('input').attr('name');

            $(domNode).find('span').hide().next().hide();

            $("<input type='text' name='"+name+"' value='"+val+"' data-old='"+val+"'>").prependTo(domNode).focus();

            $('input:text', domNode)
                .blur( this.nameApply )
                .click(function(event){ return false; })
                .keypress($.proxy( function( event ){
                    if (event.keyCode == '13') {
                        this.nameApply.call( $('input:text', domNode)[0] );
                    }
                    if (event.keyCode == '27') {
                        this.nameCancel.call( $('input:text', domNode)[0] );
                    }
                }, this));
            return false;
        },

        /**
         * Редактировать название и применить
         */
        nameApply: function () {
            var val = $( this ).val(),
                old = $( this ).attr( 'data-old' ),
                id = $( this).parent().attr( 'rel' );

            console.log('nameApply', this, id, val, old);

            if ( id && val != old ) {
                $.post( '/?route=gallery/admin', { editimage: id, name: val } );
            }
            $( this ).parent().find( 'span' ).text( val ).show().next().show().next().val( val );
            $( this ).remove();
        },

        /**
         * Редактировать название и отменить
         */
        nameCancel: function () {
            console.log('nameCancel', this);
            var val = $( this ).attr( 'data-old' );
            $( this ).parent().find( 'span' ).text( val ).show().next().show();
            $( this ).remove();
        },

        /**
         * Созранить данные об изображении
         */
        onSave: function() {
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500);
                        var domName = $('#gallery').find('li[rel='+response.id+']').find('div.gallery_name');
                        $('span', domName).text(response.name);
                        $('input.gallery_name_field', domName).val(response.name);
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
            });
        },

        /**
         * Созранить данные об категории
         */
        onSaveCat: function() {
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500);
                        $('a[rel='+response.id+']').text( response.name );
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
            });
        }
    }
});
