/**
 * Plugins for activation elFinder file manager
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   admin/jquery/jquery.filemanager.js
 */
define("admin/jquery/jquery.filemanager",[
    "jquery",
    "require",
    "jui",
    "elfinder/js/elfinder.min",
    "elfinder/js/i18n/elfinder.ru"
//    "elfinder-2.0-rc1/js/elfinder.min",
//    "elfinder-2.0-rc1/js/i18n/elfinder.ru"
], function($, require){

    $.fn.filemanager = function () {
        return $(this).each(function(){
            $(this).click(function () {
                if ($("#filemanager_dialog").length == 0) {
                    $('body').append("<div id='filemanager_dialog'></div>");
                }
                $("#filemanager_dialog").elfinder({
                    "url":"/?controller=elfinder&action=connector",
                    "lang":"ru",
                    "dialog":$.fn.filemanager.dialog
                });
                return false;
            });
        });
    };

    /**
     * Настройка диалогового окна
     * @type {Object}
     */
    $.fn.filemanager.dialog = {
        width:650,
        height:465,
        title:"Файлы",
        modal:true,
        resizable:false
    };

    /**
     * Файловый менеджер открывается на инпуте
     * @return {Boolean}
     */
    $.fn.filemanager.input = function () {

        var input = this;

        if ($("#filemanager_dialog").length == 0) {
            $('body').append("<div id='filemanager_dialog'></div>");
        }

        $("#filemanager_dialog").elfinder({
            "url":"/?controller=elfinder&action=connector",
            "lang":"ru",
            "dialog":$.fn.filemanager.dialog,
            "closeOnEditorCallback":true,
            "editorCallback":function (url) {
                $(input).val(url);
            }
        });
        return false;
    };
});

