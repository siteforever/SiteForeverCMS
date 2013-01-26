/**
 * Плагин поведений
 * @author: keltanas
 * @link http://siteforever.ru
 */

define("module/behavior",[
    "jquery"
], function( $ ){
    return {
        apply : function( object ){
            if ( object.behavior && typeof object.behavior == "object" ) {
//                console.log( object.behavior );
                $.each(object.behavior,function( selector ){
//                    console.log( this );
                    if ( typeof this == "object" ) {
                        $.each( this, function( eventType, callBack ) {
//                            console.log(eventType,selector,callBack);
                            $(document).on(
                                eventType, selector, [ callBack, object ],
                                function( event ) {
                                    return event.data[0].call( event.data[1], event, this );
                                }
                            );
                        });
                        return true;
                    }
                    console.error( typeof this + ' not supported' );
                    return false;
                });
            }
        }
    }
});