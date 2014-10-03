/**
 * Доставка
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define("market/admin/delivery", [
    "jquery",
    "system/module/modal",
    "i18n",
    "bootstrap"
],function($,Modal,i18n){
    return {
        "behavior" : {
            "a.edit" : {
                "click" : function( event, node ) {

                    $.get( $( node ).attr('href'), $.proxy(function( response ){
                        this.DeliveryEdit.body(response).show();
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
            this.DeliveryEdit = new Modal('DeliveryEdit');
            this.DeliveryEdit.title(i18n('delivery','Delivery'));

            $('#delivery tbody').sortable(this.structureSortSettings).disableSelection()
        },

        /**
         * Sortable settings
         */
        "structureSortSettings" : {
            stop : function (event, ui) {
                var positions = [];
                $('>tr', this).each(function (i) {
                    positions.push($(this).data('id'));
                });
                console.log( positions );
                $.post('/delivery/sortable/', {'sort':positions}).fail(function( response ){
                    console.error( response );
                });
            }
        }

    }
});
