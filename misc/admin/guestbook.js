/**
 * Обработчик для админки гостевой
 * @author keltanas
 */

define("admin/guestbook", [
    "jquery",
    "module/modal",
    "i18n",
    "module/alert"
],function($,Modal,i18n,$alert){
    return {
        "behavior" : {
            "a.sfcms_guestbook_edit" : {
                "click" : function( event, node ) {
                    $.get( $(node).attr('href'), $.proxy( function( response ) {
                        this.EditModal.body( response ).show();
                    }, this ) );
                    return false;
                }
            }
        },

        "init" : function() {

            this.EditModal = new Modal('EditModal');
            this.EditModal.title(i18n('guestbook', "Edit message"));
            this.EditModal.onSave(function(){
                $alert("Сохранение", $('.modal-body', this.domnode));
                $('form', this.domnode).ajaxSubmit({
                    dataType:"json",
                    success: $.proxy(function (response) {
                        if (!response.error) {
                            $.get(window.location.href, function(response){
                                var $workspace = $('#workspace');
                                $workspace.find('table').remove();
                                $workspace.find('p').remove();
                                $workspace.append(response);
                            });
                            this.msgSuccess(response.msg, 1500);
                        } else {
                            this.msgError(response.msg);
                        }
                    },this),
                    'error': $.proxy(function (response){
                        alert(response);
                    }, this)
                });
            });

        }
    }
});
