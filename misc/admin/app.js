/**
 * Admin application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

require([
    "jquery",
    "siteforever",
    "controller",
    "jui",
    "admin/jquery/jquery.filemanager",
    "admin/jquery/jquery.dumper"
],function($, $s, controller){
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
        if ( controller.behavior && typeof controller.behavior == "object") {
            $.each(controller.behavior,function( selector ){
                if ( typeof this == "object" ) {
                    $.each( this, function( eventType ) {
                        var callBack = this;
                        $(document).on(
                            eventType, selector, [ callBack, controller ],
                            function( event ){
                                return event.data[0].call( event.data[1], event, this );
                            }
                        );
                    });
                    return;
                }
                console.error( typeof this + ' not supported' );
            });
        }
    }

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();
//    $('.datepicker').datepicker( $s.datepicker );
});