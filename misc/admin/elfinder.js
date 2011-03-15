/**
 * Подключение элфиндера к CKEditor
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link http://ermin.ru
 */

$(function() {

    // CKEditor
//    var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
//    var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");
//
//    $('#finder').elfinder({
//        url : '/?route=elfinder&connector=1',
//        lang : langCode,
//        editorCallback : function(url) {
//            if ( funcNum ) {
//                window.opener.CKEDITOR.tools.callFunction(funcNum, url);
//                window.close();
//            }
//        }
//    })

    // TinyMCE
    $('#finder').elfinder({
        url : '/?route=elfinder&connector=1',
        lang : 'ru',
        editorCallback : function(url) {
            window.tinymceFileWin.document.forms[0].elements[window.tinymceFileField].value = url;
            window.tinymceFileWin.focus();
            window.close();
        }
    })


});
