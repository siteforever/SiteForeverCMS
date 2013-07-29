/**
 * Scripts for generation models
 */
(function($){
    $(document).ready(function () {
        $('a.sfcms_generation_table').click(function(){
            var table   = $(this)[0].innerHTML;
            var self    = this;
            console.log('sfcms_generation_table clicked. Value of "' + table + '"');

            if ( confirm( 'Are you really want generate models for "' + table + '" ?' ) ) {
                $.post('/generator/generate', {"table":table}).done( $.proxy(function( response ){
                    $(this).parent().find('pre' ).remove();
                    $(this).parent().append("<pre>"+response+"</pre>");
                }, this));
            }

            return false;
        });
    });
})(jQuery);
