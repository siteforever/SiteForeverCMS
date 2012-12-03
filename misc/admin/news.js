/**
 * Модуль для новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define([
    "jquery",
    "module/modal",
    "i18n",
    "module/alert",
    "siteforever",
    "jquery/jquery.form"
],function($, Modal, i18n){
    return {
        "behavior" : {
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
            },
            'a.catEdit,a.newsEdit' : {
                /**
                 * Opening edit dialog with loaded content
                 * @return {Boolean}
                 */
                "click" : function( event, node ) {
                    try {
                        $.get( $( node ).attr('href') ).then($.proxy(function( response ){
                            if ( $(node).attr('title') ) {
                                this.newsEdit.title( $(node).attr('title') );
                            }
                            this.newsEdit.body( response ).show();
                        },this));
                    } catch (e) {
                        console.error(e);
                    }
                    return false;
                }
            }
        },

        "init" : function() {
            this.newsEdit = new Modal('newsEdit');
        }
    };
});