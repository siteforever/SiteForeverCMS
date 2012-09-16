/**
 * Подключение CKEditor
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
CKEDITOR_BASEPATH = '/misc/ckeditor/';

define([
    "jquery",
    "ckeditor/ckeditor",
    "ckeditor/adapters/jquery"
],function($){
    return {
        "name" : "ckeditor",
        "init" : function() {
            $('textarea').not('.plain').each(function(){
                if ( ! CKEDITOR.instances[ $(this).attr('id') ] ) {
                    $(this).ckeditor({
                        filebrowserBrowseUrl:'/?route=elfinder/finder',
                        filebrowserImageBrowseUrl:'/?route=elfinder/finder',
                        filebrowserWindowWidth:'530',
                        filebrowserWindowHeight:'500',
                        filebrowserImageWindowWidth:'530',
                        filebrowserImageWindowHeight:'500',
//                        contentsCss: '/themes/basic/css/style.css',
                        height: 200
                    });
                }
            });

            var self = this;
            $('#finder').each(function(){
                $(this).elfinder(self.elfinder);
            });

            return 'ckeditor';
        },

        "elfinder" : {
            url : '/?route=elfinder/connector',
            lang : 'ru',
            editorCallback : function(url) {
                var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
                var langCode = window.location.search.replace(/^.*langCode=([a-z]{2}).*$/, "$1");
                if ( funcNum ) {
                    window.opener.CKEDITOR.tools.callFunction(funcNum, url);
                    window.close();
                }
            }
        },

        "destroy" : function() {
            $('textarea').not('.plain').each(function(){
                try {
                    var ed = $(this).ckeditorGet();
                    if ( ed ) ed.destroy();
                } catch (e) {
                    //console.error(e);
                }
            });
        }
    };
});


