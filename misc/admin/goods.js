define("admin/goods",[
    "jquery",
    "module/parser",
    "wysiwyg",
    "jui",
    "jquery/jquery.form"
], function( $, parser, wysiwyg ){
    return {
        "behavior" : {
            "a.edit" : {
                "click" : function ( event, node ) {
                    $.get($(node).attr('href'), $.proxy(function(response){
                        this.$dialog.html(response).dialog('open');
                    },this));
                    return false;
                }
            }
        },
        "dialog" : {
            resizable: false,
            autoOpen: false,
            height:$(window).height() - 100,
            width:950,
            modal: true,
            open: function( event, ui ) {
                wysiwyg.init();
            },
            close: function( event, ui ) {
                if ( typeof wysiwyg.destroy == 'function' ) {
                    wysiwyg.destroy();
                }
            },
            buttons: [
                {'text': 'Save', 'click' :function() {
                    $('form', this).ajaxSubmit({
                        dataType:"json",
                        success: $.proxy(function( response ){
                            if ( ! response.error ) {
                                $("#products_list").trigger("reloadGrid");
                            }
                            alert(response.msg);
                        },this)
                    });
                }},
//                {'text': 'Save & close', 'click' : function() {
//                    $( this ).dialog( "close" );
//                }},
                {'text': 'Close', 'click' :function() {
                    $( this ).dialog( "close" );
                }}
            ]
        },
        "_dialog" : 'goodsEditDialog',
        "$dialog" : null,
        "init" : function() {
            parser();
            if ( 0 == $('#'+this._dialog).length ) {
                this.$dialog = $('<div id="'+this._dialog+'" title="Edit product"/>').appendTo('body').hide().dialog(this.dialog);
            }
        }
    };
});