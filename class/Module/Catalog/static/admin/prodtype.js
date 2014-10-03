/**
 * Тип продукта
 * @author: keltanas
 * @link http://siteforever.ru
 */

define('catalog/admin/prodtype',[
    "jquery",
    "system/module/dialog",
    "i18n",
    "bootstrap",
    "jquery-ui",
    "system/jquery/jquery.form",
    "system/jquery/jquery.jqGrid"
],function( $, /*parser,*/ Dialog, i18n ){
    return {
        "behavior" : {
            "a.edit" : {
                "click" : function( event, node ) {
                    $.blockUI({message: "Loading..."});
                    this.dialog.title($(node).attr('title'));
                    $.get($(node).attr('href'), $.proxy(function(response){
                        this.dialog.body(response).open();
                    },this)).always($.unblockUI);
                    return false;
                }
            },
            "a.field-add" : {
                "click" : function( event, node ) {
                    $('tr.field-pattern').clone().removeClass('hide field-pattern')
                        .addClass('field-row').insertBefore('tr.field-pattern');
                    return false;
                }
            },
            'a.field-delete' : {
                "click" : function( event, node ) {
                    if ( confirm(i18n('Want to delete?')) ) {
                        $.get( $(node).attr('href'), function( response ){
                            if ( ! response.error ) {
                                $(node).parents('tr').remove();
                            }
                        }, 'json');
                    }
                    return false;
                }
            }
        },

        "init" : function() {
            /*parser();*/
            this.dialog = new Dialog('prodTypeDialog', this);
        },

        "onSave" : function( response ) {
            if (response.fields) {
                $('.field-row').each(function (i, fr) {
                    var field = response.fields[i],
                        $fr = $(fr);
                    if (field && field.id && !$fr.data('field-id')) {
                        $fr.attr('data-field-id', field.id);
                        $fr.find('input.field-input-id').val(field.id);
                    }
                });
            }
            $("#prodtype_list").trigger("reloadGrid");
        }
    };
});
