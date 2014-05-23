/**
 * Модуль заказа
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("market/admin/order", [
    "jquery"
],function($){
    return {
        "behavior" : {
            "a.filterEmail" : {
                "click" : function( event, node ) {
                    var filter = $(node).attr('href').replace('mailto:', '').replace(/@.+$/, '');
                    $('input.filterEmail').val(filter);
                    return false;
                }
            },
            "a.filterDate" : {
                "click" : function( event, node ) {
                    var filter = $(node).data('filter');
                    $('input.filterDate').val(filter);
                    return false;
                }
            },
            "#new_status" : {
                "change" : function(event, node) {
                    var status = $(node).val();
                    $(node).siblings('.new_status_result')
                        .stop().fadeIn(0)
                        .css("font-size", "12px")
                        .html('<img src="/images/progress-bar15.gif">');
                    $.post($(node).data('url'), {new_status: status}, function(response){
                        $(node).siblings('.new_status_result').fadeOut(1000).html(response.msg);
                    }, "json");
                }
            }
        },
        "init" : function() {
            $('.datepicker').datepicker( window.datepicker );
        }
    }
});
