/**
 * ElFinder controller
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("elfinder/admin/elfinder", [
    "jquery"
    //, "admin/jquery/elfinder/elfinder"
    , "wysiwyg"
],function( $, wysiwyg ){
    return {
        "init" : function() {
            var langCode = window.lang;
            $(window).resize(function(){
                $('#elfinder').elfinder({
                    width: $(this).width() - 2,
                    height: $(this).height() - 2/*,
                    lang: "ru"*/
                });
            });
            return $('#elfinder').elfinder(wysiwyg.elfinder).elfinder('instance');
        }
    };
});
