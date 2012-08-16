/**
 * Basic file for administrative interface
 */

$(document).ready(function () {
    /**
     * Remove page
     * Warning before remove
     */
    $('a.do_delete').each(function() {
        $(this).live('click',function () {
            if( confirm($s.i18n('The data will be lost. Do you really want to delete?')) ) {
                $.post( $( this ).attr('href') ).then( $.proxy( function(){
                    $( this ).parent().parent().hide();
                }, this));
            }
            return false;
        });
    });

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();

    /**
     * По 2х щелчку открыть менеджер файлов
     */
    $('input.image').each(function(){
        $(this).live('dblclick', $.fn.filemanager.input);
    });
});

