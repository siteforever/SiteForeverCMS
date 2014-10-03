define("catalog/admin/goods",[
    "jquery",
    "wysiwyg",
    "system/module/dialog",
    "system/admin/catalog/gallery",
    "system/admin/catalog/product",
    "bootstrap",
    "jquery-ui",
    "system/jquery/jquery.blockUI",
    "system/jquery/jquery.jqGrid"
], function( $, wysiwyg, Dialog, _gallery, _product ){

    return $.extend(true, _gallery, _product, {
        "behavior" : {
            "a.edit" : {
                "click" : function ( event, node ) {
                    $.blockUI({message: 'Loading...'});
                    this.editUrl = $(node).attr('href');
                    this.dialog.title($(node).attr('title'));
                    var params = {};
                    if ($(node).data('action') == 'add') {
                        var category = parseInt($('select[name=parent]').val(), 10) || 0,
                            type = parseInt($('select[name=type_id]').val(), 10) || 0;
                        params = {
                            'add': category,
                            'type': type
                        };
                    }
                    $.get(this.editUrl, params, $.proxy(function (response) {
                        this.dialog.body(response).open();
                    }, this)).always($.unblockUI);
                    return false;
                }
            }
        },

        "init" : function() {
            this.dialog = new Dialog('goodsEditDialog', this);
        },

        // Dialog events handlers
        "onOpen" : function() {
            wysiwyg.init();
            _gallery.sortable();
            $('.datepicker').datepicker(window.datepicker);
        },
        "onClose" : function() {
            if (typeof wysiwyg.destroy == 'function') {
                wysiwyg.destroy();
                _gallery.sortable("destroy");
            }
        },
        "onSave" : function() {
            $("#products_list").trigger("reloadGrid");
        }
    });
});
