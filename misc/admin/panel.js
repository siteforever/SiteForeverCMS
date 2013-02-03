/**
 * Frontend admin panel
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
(function($){
    var editing = false;
    $(document).ready(function(){
        $('#adminPanel a.edit').on('click', function(){
            if ( editing ) {
                $(this).text( i18n('Edit') );
                var content = $('#structure_content').val();
                $('#adminEditContent').html(content);
            } else {
                $(this).text( i18n('Save') );
                var content = $('#adminEditContent').html();

                $.get($(this).attr('href')).done(function(response){
                    $('#adminEditContent').empty().html( response );
                    wysiwyg.init();
                    $('#tabs').tabs();
                });

//                $('#adminEditContent').empty().append('<textarea></textarea>').find('textarea').val(content);
            }
            editing = ! editing;
            return false;
        });
    });
})(jQuery);