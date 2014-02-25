/**
 * Консоль
 * @author: keltanas
 */
define("module/console", function(){
    return {
        "log" : function() {
            if ( console && console.log && "function" === typeof console.log ) {
                console.log.apply( console, arguments );
            }
        },
        "error" : function() {
            if ( console && console.log && "function" === typeof console.error ) {
                console.error.apply( console, arguments );
            }
        }
    }
});
