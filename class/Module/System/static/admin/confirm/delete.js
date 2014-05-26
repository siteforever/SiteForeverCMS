/**
 * Function for confirmation before remove
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("system/admin/confirm/delete", [
    "jquery",
    "i18n",
    "jquery-ui"
], function ($, i18n) {
    return function(e) {
        e.preventDefault();
        var node = e.currentTarget,
            $dialogDeleteConfirm = $('#dialog-delete-confirm'),
            buttons = function($dialogDeleteConfirm, node) {
                return [
                    {text:i18n('Ok'),'click': function(){
                        var $dialog = $(this);
                        $dialog.html('<div class="center"><img src="/static/system/images/progress-bar.gif"></div>');
                        $.post($(node).attr('href'), function (result) {
                            if (!result.error) {
                                $('li[data-id="' + result.id + '"]').remove();
                            }
                            $dialog.dialog('close');
                            $.growlUI(i18n('Delete successfully'));
                        }, "json");
                    }},
                    {text:i18n('Cancel'), 'click': function(){
                        $(this).dialog('close');
                    }}
                ];
            };

        if (!$dialogDeleteConfirm.length) {
            $dialogDeleteConfirm = $('<div id="dialog-delete-confirm"></div>').appendTo('body');
            $dialogDeleteConfirm
                .dialog({
                    title: i18n('Want to delete?'),
                    autoOpen: false,
                    resizable: false,
                    height:140,
                    modal: true
                });
        }

        $dialogDeleteConfirm
            .dialog('option', 'buttons', buttons($dialogDeleteConfirm, node))
            .html(i18n('The data will be lost. Do you really want to delete?'))
            .dialog('open');
    };
});
