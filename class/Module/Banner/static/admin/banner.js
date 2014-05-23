/**
 * Скрипты для баннеров
 * @author keltanas@gmail.com
 */

define("banner/admin/banner", [
    "jquery",
    "system/module/modal",
    "i18n",
    "system/module/alert"
], function($, Modal, i18n, $alert){
    return {
        "behavior" : {
            'a.edit,a.cat_add,a.ban_add,#add_ban' : {
                "click" : function( event, node ) {
                    var href = $(node).attr('href');
                    var title = $(node).attr('title');
                    $.get( href, 'html' ).then(
                        $.proxy(function ( response ) {
                            this.ModalBannerEdit.title( title).body( response).show();
                        }, this));
                    return false;
                }
            },
            'a.do_delete' : {
                "click" : function( event, node ){
                    if ( ! confirm(i18n('Want to delete?')) ) {
                        return false;
                    }
                    try {
                        $.post( $(node).attr('href'), $.proxy(function(response){
                            if (!response.error) {
                                $(node).parents('tr').remove();
                            }
                            $alert(response.msg, 1500);
                        },this), "json");
                    } catch (e) {
                        console.error(e.message );
                    }
                    return false;
                }
            }
        },

        "init" : function(){
            this.ModalBannerEdit = new Modal('BannerEdit');
        }
    }
});
