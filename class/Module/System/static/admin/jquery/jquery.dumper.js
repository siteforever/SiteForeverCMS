/**
 * Plugin for activation Sypex dumper
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @file   admin/jquery/jquery.dumper.js
 */

define("system/admin/jquery/jquery.dumper",[
    "jquery"
], function($){
    $.fn.dumper = function () {
        return $(this).each(function () {
            var href    = $(this).attr("href");
            $(this).click(function () {
                if ($("#sfcms_dumper_dialog").length == 0) {
                    $('body').append("<div id='sfcms_dumper_dialog'></div>");
                    $("#sfcms_dumper_dialog").dialog({
                        autoOpen:false,
                        width:620,
                        height:510,
                        modal:true,
                        resizable:false,
                        title:'Архивация базы данных'
                    }).append("<iframe src='"+href+"'></iframe>")
                        .find('iframe')
                        .css({
                            width:'590px', height:'465px', overflow:'hidden'
                        });
                }
                $("#sfcms_dumper_dialog").dialog("open");
                return false;
            });
        });
    };
});




