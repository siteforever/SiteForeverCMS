define([
    "jquery",
    "module/parser"
], function( $, parser ){
    return {
        "init" : function() {
            parser();
        }
    };
});