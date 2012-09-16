/**
 * Basic file for administrative interface
 */
define([
    "jquery",
    "siteforever",
    "i18n",
    "admin/jquery/jquery.dumper",
    "admin/jquery/jquery.filemanager"
],function($,$s){
    /**
     * Remove page
     * Warning before remove
     */
    $('a.do_delete').live('click',function () {
        try {
            if (confirm($s.i18n('The data will be lost. Do you really want to delete?'))) {
                $.post($(this).attr('href')).then($.proxy(function () {
                    $(this).parent().parent().hide();
                }, this));
            }
        } catch (e) {
            console.error( e );
        }
        return false;
    });

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();

    /**
     * По 2х щелчку открыть менеджер файлов
     */
    $('input.image').live('dblclick', $.fn.filemanager.input);

    /**
     * Подсветка таблицы
     */
    $('table.dataset tr').hover(function () {
        $(this).addClass('select');
    }, function () {
        $(this).removeClass('select');
    });

});

