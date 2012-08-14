/**
 * Обработчик для админки гостевой
 * @author keltanas
 */
(function($,$s){
    $(document).ready(function(){
        $('a.sfcms_guestbook_edit').on('click',function(){

            var href =  $(this).attr('href');

            if ( ! $('#sfcms_guestbook_edit_dialog').length ) {
                $('<div id="sfcms_guestbook_edit_dialog"></div>').appendTo('body').dialog({
                    autoOpen: false,
                    height: 370,
                    width: 650,
                    modal: true,
                    title: $s.i18n('guestbook', "Edit message"),
                    buttons: [
                        {   'text': $s.i18n('guestbook', 'Send'),
                            'click': function() {
                                $('form',this).ajaxSubmit({
                                    'dataType': 'json',
                                    'success':$.proxy(function(response){
                                        if ( 0 == response.error ) {
                                            $(this).dialog('close');
                                        } else {
                                            $s.alert( response.msg, 2000 );
                                        }
                                    },this)
                                });
                            }
                        },
                        {   'text': $s.i18n('guestbook', 'Cancel'),
                            'click': function() {
                                $(this).dialog('close');
                            }
                        }
                    ]
                });
            }

            $.get( href, function( data ) {
                $('#sfcms_guestbook_edit_dialog').html( data ).dialog("open");
            } );

            return false;
        });
    });
})(jQuery,siteforever);