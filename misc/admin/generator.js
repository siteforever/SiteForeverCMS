/**
 * Scripts for generation models
 */
$(function () {
    $('a.sfcms_generation_table').click(function(){
        var table   = $(this)[0].innerHTML;
        var self    = this;
        console.log('sfcms_generation_table clicked. Value of "' + table + '"');

        if ( confirm( 'Are you really want generate models for "' + table + '" ?' ) ) {
            $.post('/generator/generate', {"table":table}, function( response ){
                $(self).parent().append("<code>"+response+"</code>");
                console.log( response );
            });
        }

        return false;
    });
});
