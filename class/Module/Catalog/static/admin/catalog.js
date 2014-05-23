/**
 * Скрипты для каталога
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
define("catalog/admin/catalog", [
    "jquery",
    "system/module/modal",
    "i18n",
    "system/module/alert",
    "system/admin/catalog/gallery",
    "system/admin/catalog/product",
    "jquery-ui"
],function( $, Modal, i18n, $alert, _gallery, _product ){

    return $.extend(true, _gallery, _product, {

        "behavior" : {

            "a.edit" : {
                "click" : function( event, node ) {
                    this.editUrl = $(node).attr('href');
                    this.Modal.title("Правка ");
                    $.get( this.editUrl, $.proxy(function( response ){
                        this.Modal.body( response ).show();
                    },this));
                    return false;
                }
            },

            // Switch on/off page
            "a.order_hidden" : {
                "click" : function ( event, node ) {
                    $.get( $( node ).attr('href'), function ( data ) {
                        $( node ).replaceWith( data );
                    });
                    return false;
                }
            },

            'a.do_delete' : {
                'click' : function( event, node ) {
                    try {
                        if (confirm(i18n('Want to delete?'))) {
                            $.post( $(node).attr('href'), $.proxy(function( response ){
                                if( ! response.error ) {
                                    $(node).parents('tr').remove();
                                }
                            }, this), 'json' );
                        }
                    } catch (e) {
                        console.error(e.message);
                    }
                    return false;
                }
            },

            '#catalog_save_position' : {
                "click" : function( event, node ){
                    $alert('Сохранение...');
                    var pos = [];
                    $('input.trade_pos').each( function(){
                        pos.push( { key: $(this).attr('rel'), val: $(this).val() } );
                    });
                    $.post( $( node ).attr('href'), { "save_pos": pos }, function( data ){
                        document.location.reload();
                    });
                }
            },

            'a.catalog_switch' : {
                "click" : function( event, node ){
                    $.post( $( node ).attr('href'), function( data ) {
                        if ( data.error == '0' ) {
                            $( node ).attr( 'href', data.href ).html( data.img );
                        }
                    }, 'json');
                    return false;
                }
            },

            // Фильтрация товаров
            '#goods_filter_select' : {
                "click" : function( event, node ){
                    var href = window.location.href;
                    href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
                    if ( $('#goods_filter').val() != '' ) {
                        href += '/' + 'goods_filter=' + $('#goods_filter').val();
                    }
                    window.location.href = href;
                }
            },

            // Отмена фильтрации
            '#goods_filter_cancel' : {
                "click" : function( event, node ){
                    var href = window.location.href;
                    href = href.replace(/\/$/, '').replace(/(\/goods_filter=[^\/]+?)*$/, '');
                    window.location.href = href;
                }
            }
        },

        "init" : function(){
            this.Modal = new Modal('CatalogEdit');
        }
    });
});
