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

