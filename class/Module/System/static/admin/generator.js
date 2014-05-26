/**
 * Scripts for generation models
 */
define("system/admin/generator", [
"jquery"
], function($){
    $(document).ready(function () {
        $('a.sfcms_generation_table').click(function(){
            var table   = $(this)[0].innerHTML;
            if ( confirm( 'Are you really want generate models for "' + table + '" ?' ) ) {
                $.post('/generator/generate', {"table":table}).done( $.proxy(function( response ){
                    $(this).parent().find('pre' ).remove();
                    $(this).parent().append("<pre>"+response+"</pre>");
                }, this));
            }

            return false;
        });
    });
});
