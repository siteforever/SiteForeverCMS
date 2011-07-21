/**
 * Подключение CKEditor
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

var wysiwyg = {
    init: function() {
        var editor = $('textarea').not('.plain').ckeditor({
            filebrowserBrowseUrl 		: '/?controller=elfinder&action=index&finder=1',
            filebrowserImageBrowseUrl 	: '/?controller=elfinder&action=index&finder=1',
            filebrowserWindowWidth : '530',
            filebrowserWindowHeight : '500',
            filebrowserImageWindowWidth : '530',
            filebrowserImageWindowHeight : '500'
        });

        var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
        var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");

        if ( $('#finder').length == 1 ) {
            $('#finder').elfinder({
                url : '/?route=elfinder&connector=1',
                lang : langCode,
                editorCallback : function(url) {
                    if ( funcNum ) {
                        window.opener.CKEDITOR.tools.callFunction(funcNum, url);
                        window.close();
                    }
                }
            })
        }
        return 'ckeditor';
    }
}

$(function(){
    wysiwyg.init();
})


