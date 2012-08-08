/**
 * Скрипты для баннеров
 * @author keltanas@gmail.com
 */

siteforever.banner = {};

siteforever.banner.dialogSettings = {
    autoOpen: false,
    width:    735,
    modal:    true,
    open:     function () {
        wysiwyg.init();
    },
    buttons:  {
        "Отмена":    function () {
            $( this ).dialog( "close" );
        },
        "Сохранить": function () {
            var self = this,
                url  = $('form', this )[0].getAttribute('action');
            $( 'form', this ).ajaxSubmit( {
                type:    'POST',
                url:     url,
                success: function ( response, textStatus, jqXHR ) {
                    try {
                        $( self ).dialog( "close" );
                    } catch ( e ) {
                        console.error( e.message );
                        return false;
                    }
                    sf.alert( response, 2000 );
                    return true;
                },
                error:   function ( XMLHttpRequest, textStatus, errorThrown ) {
                    $( self ).dialog( "close" );
                    sf.alert( 'Данные не сохранены', 2000 );
                    return true;
                }
            } );
        }
    }
};
$(function(){

    var dialogForm = $('<div id="dialog-form"></div>').appendTo('body');
    dialogForm.dialog( siteforever.banner.dialogSettings );

    $( 'a.cat_add,a.ban_add,#add_ban' ).each( function () {
        $( this ).click( function ( event ) {
            event.stopPropagation();
            var href = $(this).attr('href');
            var title = $(this).attr('title');
            sf.alert("Загрузка данных");
            $.get( href, 'html' ).then(
                $.proxy(function ( response ) {
                    sf.alert.close();
                    dialogForm.html( response ).dialog( 'option', 'title', title );
                    dialogForm.dialog( 'open' );
                }, this),
                $.proxy(function ( error ) {
                }, this)
            );
            return false;
        } );
    } );

});
