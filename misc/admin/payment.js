/**
 * Модуль оплат
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define([
    "jquery",
    "module/modal",
    "i18n"
], function($,Modal,i18n){
    return {
        "behavior" : {
            "a.edit" : {
                "click" : function( event, node ) {
                    $.get( $(node).attr('href'), $.proxy(function( response ){
                        this.ModalEdit.title(i18n('Edit')).body( response).show();
                    },this));
                    return false;
                }
            }
        },

        "init" : function() {
            this.ModalEdit = new Modal('PaymentModalEdit');
        }
    }
});