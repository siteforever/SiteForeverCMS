/**
 * Модуль заказа
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define([
    "jquery",
    "siteforever"
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
            }
        },
        "init" : function() {
            $('.datepicker').datepicker( window.datepicker );
        }
    }
});