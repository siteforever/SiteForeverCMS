/**
 * Модуль пользователей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("admin/user", [
    "jquery",
    "module/modal",
    "siteforever"
], function( $, Modal ){
    return {
        "behavior" : {
            "a.edit" : {
                "click" : function( event, node ) {
                    $.get($(node).attr('href'), $.proxy(function(response){
                        this.ModalUserEdit.title($(node).attr('title')).body( response).show();
                    },this));
                    return false;
                }
            }
        },

        "init" : function() {
            this.ModalUserEdit = new Modal('UserEdit');
        }
    }
});
