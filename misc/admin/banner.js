/**
 * Скрипты для баннеров
 * @author keltanas@gmail.com
 */
$(function(){

    var dialogForm = $('<div id="dialog-form"></div>').appendTo('body');

    $( 'a.cat_add,a.ban_add,#add_ban' ).each( function () {
        $( this ).bind( 'click', function ( event ) {
            var href = $(this).attr('href');
            var title = $(this).attr('title');
            $.post( href ).then(
                $.proxy(function ( response ) {
                    dialogForm.html( response );
                    dialogForm.dialog( 'option', 'title', title );
                    dialogForm.dialog( 'open' );
                }, this),
                $.proxy(function ( error ) {
                    alert( error );
                }, this)
            );
            return false;
        } );
    } );

    dialogForm.dialog( {
        autoOpen: false,
        width:    735,
        modal:    true,
        open:     function () {
            wysiwyg.init();
        },
        buttons:  {
            "Отмена":    function () {
                $( this ).dialog( "close" );
                $( this ).find( 'div' ).remove();
            },
            "Сохранить": function () {
                var self = this,
                    url  = $('form', this )[0].getAttribute('action');
                $( 'form', this ).ajaxSubmit( {
                    type:    'POST',
                    url:     url,
                    success: function ( response, textStatus, jqXHR ) {
                        $( self ).dialog( "close" );
                        $.showBlock( response );
                        $.hideBlock( 2000 );
//                        window.location.reload()
//                        return true;
                    },
                    error:   function ( XMLHttpRequest, textStatus, errorThrown ) {
                        $( self ).dialog( "close" );
                        $.showBlock( 'Данные не сохранены' );
                        $.hideBlock( 2000 );
//                        return true;
                    }
                } );
            }
        },

        beforeClose: function ( event, ui ) {
            $( this ).find( 'div' ).remove()
        },
        close:       function () {
            $( this ).find( 'div' ).remove();
        }
    } );
});
