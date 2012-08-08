/**
 * Модуль для новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
(function($, $s){

    $s.news = {
        dialogEditCat: {
            'autoOpen': false,
            'modal':    true,
            'resizable': false,
            'width':    700,
            'open': function() {
                $('.datepicker').datepicker($s.datepicker);
                $( "#tabs" ).tabs();
                wysiwyg.init();
            },
            'title':    $s.i18n('news','News category')
        },
        save: {
            'text': $s.i18n('Save'),
            'click': function() {
                var self = this;
                $('form',this).ajaxSubmit({
                    'dataType': 'json',
                    'success': function( response ) {
                        $s.alert( response.msg, 2000).done(function(){
                            if ( 0 == response.error ) {
                                document.location.reload();
                                //$(self).dialog('close');
                            }
                        });
                    }
                });
            }
        },
        cancel: {
            'text': $s.i18n('Cancel'),
            'click': function() {
                $(this).dialog('close');
            }
        }
    };
    $s.news.dialogEditCat.buttons = [ $s.news.save, $s.news.cancel ];


    /**
     * Init
     */
    $(document).ready(function(){
        if ( ! $('#newsEditDialod').length ) {
            $('<div id="newsEditDialod"></div>').appendTo('body').hide().dialog( $s.news.dialogEditCat );
        }

        $('a.catEdit,a.newsEdit').on('click', function(){
            $('#newsEditDialod').dialog('option', 'title', $(this).attr('title'));
            $.get($(this).attr('href')).done( function (response) {
                $('#newsEditDialod').html(response).dialog('open');
            } );
            return false;
        });
    });

})(jQuery, siteforever);