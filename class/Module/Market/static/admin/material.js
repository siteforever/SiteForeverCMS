/**
 * Модуль материалов
 * @author: keltanas
 * @link http://siteforever.ru
 */
define("market/admin/material", [
    "jquery",
    "system/module/modal",
    "i18n",
    "jquery-form",
    "bootstrap"
],function ($, Modal) {
    return {
        "init" : function(){
            this.editModal = new Modal('MaterialEdit');
            this.editModal.onSave(function(){
                $('form', this.domnode).ajaxSubmit({
                    dataType:"json",
                    success: $.proxy(function( response ){
                        if ( ! response.error ) {
                            this.msgSuccess( response.msg, 1000).done(function(){
                                $("#material_list").trigger("reloadGrid");
                            });
                        } else {
                            this.msgError( response.msg );
                        }
                    },this)
                });
            });
        },
        "behavior" : {
            "a.edit" : {
                'click' : function( event, node ) {
                    $.get( $(node).attr('href') ).done($.proxy(function( response ){
                        this.editModal.title( $(node).attr('title') || 'title' ).body( response ).show();
                    }, this));
                    return false;
                }
            }
        }
    };
});
