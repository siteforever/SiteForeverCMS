/**
 * Подключение TinyMCE
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
$(function(){

    $('textarea').not('.plain').tinymce({
        // Location of TinyMCE script
        script_url :    '/misc/tinymce/jscripts/tiny_mce/tiny_mce.js',
        // General options
        theme :         'advanced',
        language:       'ru',
        convert_urls :  false,
        // Theme options
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        // Connect ElFinder
        file_browser_callback : function(field_name, url, type, win) {
            var w = window.open('/?controller=elfinder&action=index&finder=1', null, 'width=600,height=420');
            // Сохраняем необходимые параметры в глобальных переменных окна (не самое лучшее решение, предложите другое?),
            // или можете передавать параметры в GET и потом разбирать их в elfinder.html
            w.tinymceFileField = field_name;
            w.tinymceFileWin = win;
        }
    });

    if ( $('#finder').length == 1 ) {
        $('#finder').elfinder({
            url : '/?route=elfinder&connector=1',
            lang : 'ru',
            editorCallback : function(url) {
                window.tinymceFileWin.document.forms[0].elements[window.tinymceFileField].value = url;
                window.tinymceFileWin.focus();
                window.close();
            }
        })
    }
})