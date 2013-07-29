/**
 * Управление админкой производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
define("admin/manufacturers", [
    "jquery",
    "module/modal",
    "siteforever"
],function($, Modal){
    return {
        "init" : function() {
            $( 'a.do_delete').each(function(){
                $(this).on('click', function(){
                    return confirm( $( this ).attr('title') );
                });
            });

            var ManufEdit = new Modal('ManufEdit');
            $( 'a.edit' ).each(function(){
                $(this).on('click', function(){
                    $.get( $(this).attr('href') ).done($.proxy(function(response){
                        ManufEdit.title( $(this).attr('title') ).body( response ).show();
                    }, this));
                    return false;
                });
            });
        }
    };
});
