/**
 * Обработчик для админки гостевой
 * @author keltanas
 */

$(function(){
    $('a.sfcms_guestbook_edit').click(function(){

        var href =  $(this).attr('href');

        if ( ! $('#sfcms_guestbook_edit_dialog').length ) {
            $('body').append('<div id="sfcms_guestbook_edit_dialog"></div>');
            $('#sfcms_guestbook_edit_dialog').dialog({
                autoOpen: false,
                height: 500,
                width: 760,
                modal: true,
                title: "Редактирование сообщения",
                buttons: {
                    "Отправить": function() {
                        $('#sfcms_guestbook_edit_dialog').find('form').submit();
                    }
                }
            });
        }

        $.post( href, function( data ) {
            $('#sfcms_guestbook_edit_dialog').html( data ).dialog("open");
        } );

        return false;
    });
});