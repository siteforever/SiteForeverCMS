/**
 * Управление админкой производителей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 */
$(document).ready(function(){
    $( 'a.delete' ).click(function(){
        if ( confirm( $(this ).attr('title') ) ) {
            $.post( $( this ).attr('href') ).then( $.proxy( function( response ){
                $s.alert( response, 1000 ).done( $.proxy( function(){
                    $( this ).parent().parent().remove();
                }, this) );
            }, this ));
        }
        return false;
    });
});