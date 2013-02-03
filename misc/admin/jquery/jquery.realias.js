/**
 * Перерасчет алиасов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @file   admin/jquery/jquery.realias.js
 */

define("admin/jquery/jquery.realias",[
    "jquery",
    "i18n",
    "module/alert"
], function($,i18n,$alert){
    $.fn.realias = function () {
        return $(this).live('click',function () {
            if ($("#realias_dialog").length == 0) {
                $('body').append("<div id='realias_dialog'></div>");
                $("#realias_dialog").dialog({
                    autoOpen:false,
                    width:650,
                    height:465,
                    modal:true,
                    resizable:false,
                    title:'Пересчет алиасов',
                    buttons: [
                        {
                            text: i18n('Close'),
                            click: function() {
                                $(this ).dialog('close');
                            }
                        }
                    ]
                });
            }
            $alert('<p>Ведется пересчет...<br />Не закрывайте окно.</p>');
            $.post($(this).attr('href'), function (request) {
                $alert.close();
                $("#realias_dialog").html(request).dialog("open");
            });
            return false;
        });
    };
});
