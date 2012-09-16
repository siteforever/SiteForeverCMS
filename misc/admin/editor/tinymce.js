/**
 * Подключение TinyMCE
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
define([
    "jquery",
    "tiny_mce/jquery.tinymce"
], function($){
    return {
        init : function() {
            var ret = $('textarea').not('.plain').tinymce({
                // Location of TinyMCE script
                "script_url" :    '/misc/tiny_mce/tiny_mce.js',
                // General options
                "width" :           '650',
                "height" :          '300',
                "theme" :           'advanced',
                "skin" :            'default',
                "language":         'ru',
                "convert_urls" :    false,
                "plugins" :         "paste,table,media",
                "paste_auto_cleanup_on_paste" : true,
                "theme_advanced_buttons1_add" : "|,table,media",
                "theme_advanced_buttons2_add_before" : "pasteword,|",
                "extended_valid_elements" : "div[*],p[*]",
//                "content_css" : "/themes/basic/css/style.css",
                // Theme options
                "theme_advanced_toolbar_location" : "top",
                "theme_advanced_toolbar_align" : "left",
                "theme_advanced_statusbar_location" : "bottom",
                // Connect ElFinder
                "file_browser_callback" : function(field_name, url, type, win) {
                    var w = window.open('/?route=elfinder/finder', null, 'width=600,height=420');
                    // Сохраняем необходимые параметры в глобальных переменных окна (не самое лучшее решение, предложите другое?),
                    // или можете передавать параметры в GET и потом разбирать их в elfinder.html
                    w.tinymceFileField = field_name;
                    w.tinymceFileWin = win;
                }
            });
            var self = this;
            $('#finder').each(function(){
                $(this).elfinder(self.elfinder);
            });
            return 'tinymce';
        },

        "elfinder" : {
            url             : '/?route=elfinder/connector',
            lang            : 'ru',
            editorCallback  : function(url) {
                window.tinymceFileWin.document.forms[0].elements[window.tinymceFileField].value = url;
                window.tinymceFileWin.focus();
                window.close();
            }
        }
    }
});
