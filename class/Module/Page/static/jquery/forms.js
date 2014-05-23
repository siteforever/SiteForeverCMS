/**
 * Forms application for jquery forms plugin
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 * @file   /misc/admin/forms.js
 */

(function($){
    $(document).ready(function () {
        // output uri
        $('#structure_uri,#structure_alias').bind('keypress', function (event) {
            if ( ! ( event.keyCode == 8 || event.keyCode == 9 ||
                ( event.keyCode >= 33 && event.keyCode <= 40 ) ||
                ( event.keyCode >= 45 && event.keyCode <= 47 ) ||
                ( event.charCode >= 47 && event.charCode <= 57 ) ||
                ( event.charCode >= 95 && event.charCode <= 122 && event.charCode != 96 )
                ) ) {
                event.preventDefault();
            }
        });

        $('form.module_form, form.ajax').ajaxForm({
            beforeSubmit:function ( arr, $form, options ) {
                options.url = $($form)[0].action;
                $.blockUI( 'Отправка данных...' );
            },
            success:function (data) {
                $('div.blockMsg').html(data);
                setTimeout(function(){
                    $.unblockUI();
                },1000);
            },
            iframe:false
        }).find("input:text").live('keypress', function (e) {
            if (e.keyCode == 13) {
                return false;
            }
        });

        $('.datepicker').datepicker(window.datepicker);


        $(':reset').click(function () {
            $(this).parents("form").clearForm().submit();
            return false;
        });

    });
})(jQuery);

