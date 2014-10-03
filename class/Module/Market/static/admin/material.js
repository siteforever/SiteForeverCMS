/**
 * Модуль материалов
 * @author: keltanas
 * @link http://siteforever.ru
 */
define("market/admin/material", [
    "query",
    "system/module/parser",
    "system/module/modal",
    "i18n",
    "system/jquery/jquery.form",
    "bootstrap"
],function ($, parser, Modal) {
    return {
        "init" : function(){
            parser();
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
