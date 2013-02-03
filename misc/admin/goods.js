define("admin/goods",[
    "jquery",
//    "module/parser",
    "wysiwyg",
    "module/dialog",
    "admin/catalog/gallery",
    "admin/catalog/product",
    "jui",
    "jquery/jquery.blockUI",
    "jquery/jquery.jqGrid",
    "siteforever"
], function( $, /*parser,*/ wysiwyg, Dialog, _gallery, _product ){

    return $.extend(true, _gallery, _product, {
        "behavior" : {
            "a.edit" : {
                "click" : function ( event, node ) {
                    this.editUrl = $(node).attr('href');
                    this.dialog.title($(node).attr('title'));
                    var params = {};
                    if ( $(node).data('action') == 'add' ) {
                        var category = parseInt($('select[name=parent]').val()),
                            type     = parseInt($('select[name=type_id]').val());
//                        console.log( category, parseInt(category.val()), !!parseInt(category.val()), type, parseInt(type.val()), !!parseInt(type.val()) );
                        if ( ! category ) {
                            alert('Укажите категорию');
                            return false;
                        }
                        if ( ! type ) {
                            alert('Укажите тип');
                            return false;
                        }
                        params = {
                            'add' : category,
                            'type' : type
                        };
                    }
                    $.get( this.editUrl, params, $.proxy(function( response ){
                        this.dialog.body( response ).open();
                    },this));
                    return false;
                }
            }
        },

        "init" : function() {
//            parser();
            this.dialog = new Dialog( 'goodsEditDialog', this );
        },

        // Dialog events handlers
        "onOpen" : function() {
            wysiwyg.init();
            _gallery.sortable();
            $('.datepicker').datepicker( window.datepicker );
        },
        "onClose" : function() {
            if ( typeof wysiwyg.destroy == 'function' ) {
                wysiwyg.destroy();
            }
        },
        "onSave" : function() {
            $("#products_list").trigger("reloadGrid");
        }
    });
});