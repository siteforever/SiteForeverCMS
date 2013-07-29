/**
 * ElFinder
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("admin/elfinder", [
    "jquery",
    "wysiwyg",
    "elfinder/js/elfinder.full",
    "elfinder/js/i18n/elfinder.ru"
],function( $, wysiwyg ){
    return {
        "init" : function() {
            console.log( wysiwyg );
            var langCode = 'ru';
            $('#finder').each(function(){
                $(this).elfinder(wysiwyg.elfinder);
            });
        }
    };
});
