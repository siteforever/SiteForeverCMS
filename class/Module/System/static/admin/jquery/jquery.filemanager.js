/**
 * Integration elFinder file manager
 * It is no longer a jquery module
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   admin/jquery/jquery.filemanager.js
 */
define("system/admin/jquery/jquery.filemanager",[
    "jquery",
    "jquery-ui",
    "elfinder"
], function($){

    /** cache for jquery elFinder node */
    var $filemanager = $("#filemanager_dialog"),

        /** begin settings for elFinder */
        filemanager = {
            url: "/elfinder/connector",
            lang: window.lang,
            resizable: false,
            debug: false
        },

        /**
         * Settings for ui.dialog
         * @type {Object}
         */
        dialog  = {
            width: $(window).width() - 50,
            height: $(window).height() - 50,
            modal:true,
            resizable:false,
            close: function() {
                $(this).elfinder('destroy').dialog('destroy');
            },
            open: function(getFileCallback, event) {
                var opt = $.extend({
                    width: $(event.target).width(),
                    height: $(event.target).height()
                }, filemanager);
                if (getFileCallback && typeof getFileCallback == 'function') {
                    opt.getFileCallback = getFileCallback;
                }
                $(event.target).elfinder(opt);
            }
        };

    /**
     * Creating node for file manager if not exists
     */
    if (!$filemanager.length) {
        $filemanager = $("<div id='filemanager_dialog'></div>")
            .appendTo('body')
            .css({"padding": 0, "overflow": "hidden"});
    }

    /**
     * File manager open by a.filemanager
     */
    $(document).on('click', 'a.filemanager', function(){
        var opt = $.extend({}, dialog, {
            open: $.proxy(dialog.open, this, undefined),
            title: $(this).text()
        });
        $filemanager.dialog(opt);
        return false;
    });

    /**
     * File manager open by input.image
     */
    $(document).on('dblclick', 'input.image', function () {
        var node = this;
        var opt = $.extend({}, dialog, {
            open: $.proxy(dialog.open, this, function(file){
                $(node).val(file.url);
                $filemanager.dialog('close');
            }),
            title: $(node).parent().siblings("label").text()
        });
        $filemanager.dialog(opt);
        return false;
    });

    return filemanager;
});

