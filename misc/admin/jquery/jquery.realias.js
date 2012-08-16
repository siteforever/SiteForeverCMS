/**
 * Перерасчет алиасов
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @file   admin/jquery/jquery.realias.js
 */

$.fn.realias = function () {
    return $(this).each(function () {
        $(this).live('click',function () {
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
                            text: $s.i18n('Close'),
                            click: function() {
                                $(this ).dialog('close');
                            }
                        }
                    ]
                });
            }
            $s.alert('<p>Ведется пересчет...<br />Не закрывайте окно.</p>');
            $.post($(this).attr('href'), function (request) {
                $s.alert.close();
                $("#realias_dialog").html(request).dialog("open");
            });
            return false;
        });
    });
}
