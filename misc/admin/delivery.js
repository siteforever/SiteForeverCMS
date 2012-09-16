/**
 * Доставка
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
define([
    "jquery",
    "module/Modal",
    "i18n"
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