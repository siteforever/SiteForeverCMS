/**
 * Скрипты для баннеров
 * @author keltanas@gmail.com
 */
$( function () {

    $('body').append('<div id="dialog-form"></div>');

    $( 'a.cat_add,a.ban_add,#add_ban' ).each( function () {
        $( this ).bind( 'click', function ( event ) {
            $.post( this.getAttribute( 'href' ), function ( response ) {
                $( "#dialog-form" ).append( '<div>' + response + '</div>' ).dialog( "open" );
            } );
            return false;
        } );
    } );

    $( "#dialog-form" ).dialog( {
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
                        window.location.reload()
                        return true;
                    },
                    error:   function ( XMLHttpRequest, textStatus, errorThrown ) {
                        $( self ).dialog( "close" );
                        $.showBlock( 'Данные не сохранены' );
                        $.hideBlock( 2000 );
                        return true;
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
} );