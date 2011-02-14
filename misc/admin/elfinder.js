/**
 * Подключение элфиндера к CKEditor
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

$(function() {

    var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
    var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");

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

});
