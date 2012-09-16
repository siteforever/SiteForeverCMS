/**
 * Обработчик для админки гостевой
 * @author keltanas
 */

define([
    "jquery",
    "module/modal",
    "i18n"
],function($,Modal,i18n){
    return {
        "behavior" : {
            "a.sfcms_guestbook_edit" : {
                "click" : function( event, node ) {
                    $.get( $(node).attr('href'), $.proxy( function( response ) {
                        this.EditModal.body( response ).show();
                    }, this ) );
                    return false;
                }
            }
        },

        "init" : function() {

            this.EditModal = new Modal('EditModal');
            this.EditModal.title(i18n('guestbook', "Edit message"));

        }
    }
});
