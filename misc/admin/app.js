/**
 * Admin application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

require([
    "jquery",
    "controller",
    "module/behavior",
    "siteforever",
    "jui",
    "admin/jquery/jquery.filemanager",
    "admin/jquery/jquery.dumper"
],function($, controller, behavior){
    if ( controller ) {
        /**
         * Run init
         */
        if ( controller.init && typeof controller.init == "function") {
            controller.init();
        }

        /**
         * Apply behaviors
         */
        behavior.apply( controller );
    }

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();

    /**
     * Placeholder while initialisation
     */
    $('#loading-application').each(function(){
        $(this).fadeOut(200,function(){
            $(this).remove();
        });
    });
//    $('.datepicker').datepicker( window.datepicker );
});