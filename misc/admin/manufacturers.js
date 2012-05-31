/**
 * Управление админкой производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
$(function(){
    $( 'a.delete' ).click(function(){
        if ( confirm('Желаете удалить?') ) {
            $.post( $( this ).attr('href'), $.proxy( function( response ){
                sf.alert( response, 1000 ).done( $.proxy( function(){
                    $( this ).parent().parent().remove();
                }, this) );
            }, this ));
        }
        return false;
    });
});