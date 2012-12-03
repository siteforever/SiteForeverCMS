/**
 * Basic file for administrative interface
 */
define("admin/admin",[
    "jquery",
    "i18n",
    "siteforever",
    "admin/jquery/jquery.dumper",
    "admin/jquery/jquery.filemanager"
],function($,i18n){

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

