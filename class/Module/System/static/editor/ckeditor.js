/**
 * Подключение CKEditor
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
define('system/editor/ckeditor',[
    "module",
    "jquery",
    "system/admin/jquery/jquery.filemanager",
    "ckeditor/ckeditor",
    "ckeditor/adapters/jquery"
],function(module, $, filemanager){

    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
        var match = window.location.search.match(reParam) ;

        return (match && match.length > 1) ? match[1] : '' ;
    }

    var funcNum = getUrlParam('CKEditorFuncNum'),
        finderUrl = '/elfinder/finder',
        finderWidth = 800,
        finderHeight = 600;

    return {
        "name" : "ckeditor",
        "init" : function() {
            $('textarea').not('.plain').each(function(){
                if (!CKEDITOR.instances[ $(this).attr('id') ]) {
                    CKEDITOR.basePath = '/static/lib/ckeditor/';
                    CKEDITOR.plugPath = '/static/lib/ckeditor/plugins/';
                    $(this).ckeditor({
                        filebrowserBrowseUrl:finderUrl,
                        filebrowserImageBrowseUrl:finderUrl,
                        filebrowserWindowWidth:finderWidth,
                        filebrowserWindowHeight:finderHeight,
                        filebrowserImageWindowWidth:finderWidth,
                        filebrowserImageWindowHeight:finderHeight,
                        contentsCss: module.config().style,
                        height: 300
                    });
                }
            });
            return 'ckeditor';
        },

        "elfinder" : $.extend({}, filemanager, {
            width: $(window).width() - 2,
            height: $(window).height() - 2,
            getFileCallback : function(file) {
                window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
                window.close();
            }
        }),

        "destroy" : function() {
            $('textarea').not('.plain').each(function(){
                try {
                    var ed = $(this).ckeditorGet();
                    if ( ed ) ed.destroy();
                } catch (e) {
                }
            });
        }
    };
});


