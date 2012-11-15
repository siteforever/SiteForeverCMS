define([
    "jquery",
    "module/parser"
//    "jquery/jquery.jqGrid"
], function( $, parser ){
    $(document).ready(function(){
        parser();
    });
});