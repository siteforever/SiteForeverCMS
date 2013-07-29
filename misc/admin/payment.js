/**
 * Модуль оплат
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("admin/payment", [
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
            },
            "a.do_delete" : {
                "click" : function( event, node ) {
                    if ( confirm(i18n('Want to delete?')) ) {
                        $.get( $(node).attr('href'), $.proxy(function( response ){
                            if ( response.id ) {
                                $('tr.row-'+response.id).remove();
                            }
                        }, this), "json");
                    }
                    return false;
                }
            }
        },

        "init" : function() {
            this.ModalEdit = new Modal('PaymentModalEdit');
        }
    }
});
