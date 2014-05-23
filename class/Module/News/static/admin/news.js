/**
 * Модуль для новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("news/admin/news", [
    "jquery",
    "system/module/modal",
    "i18n",
    "system/module/alert",
    "system/jquery/jquery.form"
],function($, Modal, i18n, $alert){
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
                        $.get($(node).attr('href')).then($.proxy(function (response) {
                            if ($(node).attr('title')) {
                                this.newsEdit.title($(node).attr('title'));
                            }
                            this.newsEdit.body(response).show();
                        }, this));
                    } catch (e) {
                        console.error(e);
                    }
                    return false;
                }
            }
        },

        "init" : function() {
            this.newsEdit = new Modal('newsEdit');
            this.newsEdit.onSave(function(){
                $alert("Сохранение", $('.modal-body', this.domnode));
                $('form', this.domnode).ajaxSubmit({
                    dataType:"json",
                    success: $.proxy(function (response) {
                        if (!response.error) {
                            $.get(window.location.href, function(response){
                                var $workspace = $('#workspace');
                                $workspace.find(':not(h2)').remove();
                                $workspace.append(response);
                            });
                            this.msgSuccess(response.msg, 1500);
                        } else {
                            this.msgError(response.msg);
                        }
                    },this)
                });
            });
        }
    };
});
