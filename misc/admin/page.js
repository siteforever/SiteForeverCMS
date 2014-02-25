/**
 * Модуль управления страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("admin/page", [
    "jquery",
    "module/modal",
    "module/dialog",
    "i18n",
    "module/alert",
    "module/console",
    "wysiwyg",
    "jui",
    "jquery/jquery.form",
    "admin/admin"
], function($, Modal, Dialog, i18n, $alert, console, wysiwyg) {

    var dialogCreateSave = function(){
            var $name = $('#name');
            $name.parent().find('.help-inline').remove();
            if (!$.trim($name.val())) {
                $name.parent().append('<div class="help-inline">'+i18n('Input Name')+'</div>');
                $name.parents('.control-group').addClass('error');
                return;
            } else {
                $name.parents('.control-group').removeClass('error');
            }

            this.$createDialog.block({'message': 'Отправка'});
            // page/add
            $.post($('#url').val(), {
                'module': $('#module').val(),
                'name': $name.val(),
                'parent': $('#id').val()
            }).then($.proxy(function (response) {
                this.$createDialog.unblock().html('').dialog('close');
                this.$editDialog.html(response).dialog("option", "title", i18n('Create page')).dialog('open');
            }, this));
        },

        dialogCreate = {
            title: 'Создать страницу',
            autoOpen: false,
            modal: true,
            width: 500,
            buttons: {
                "Закрыть": function() {
                    $(this).dialog('close');
                }
            }
        },


        dialogEditSave = function(){
            var $form = $('form', this),
                defer = $.Deferred(),
                timoutDefer = $.Deferred(),
                ajaxDefer = $.Deferred();
            $form.block({message: 'Saving'});
            $form.ajaxSubmit({
                dataType:"json",
                success: $.proxy(function (response) {
                    if (!response.error) {
                        $form.unblock().block({message: response.msg});
                        setTimeout(function(){
                            $form.unblock();
                            timoutDefer.resolve();
                        }, 1500);
                        $.get('/page/admin' ).then(function(response){
                            $('#structureWrapper').find('.b-main-structure').empty()
                                .html($(response).find('.b-main-structure').html());
                            $('div.b-main-structure').find('ul').sortable(this.struntureSortSettings).disableSelection();
                            ajaxDefer.resolve();
                        });
                    } else {
                        $form.unblock();
                        timoutDefer.reject();
                        ajaxDefer.reject();
                    }
                }, this),
                'error': $.proxy(function (response){
                    console.log(arguments);
                    alert(response.responseText);
                    timoutDefer.reject();
                    ajaxDefer.reject();
                }, this)
            });
            $.when(timoutDefer, ajaxDefer).then(defer.resolve, defer.reject);
            return defer.promise();
        },

        dialogEdit = {
            autoOpen: false,
            modal: true,
            width: 950,
            open: function(){
                $('.datepicker').datepicker( window.datepicker );
                wysiwyg.init();
            },
            close: function(){
                if (typeof wysiwyg.destroy == 'function') {
                    wysiwyg.destroy();
                }
            },
            buttons: {
                "Сохранить": dialogEditSave,
                "Сохранить и закрыть": function() {
                    dialogEditSave.apply(this).always($.proxy(function(){
                        $(this).dialog('close');
                    }, this));
                },
                "Закрыть": function() {
                    $(this).dialog('close');
                }
            }
        };

    return {
        "progressTpl": '<div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div>',

        "behavior" : {
            // Подсветка разделов структуры
            'div.b-main-structure span' : {
                "mouseover" : function ( event, node ) {
                    $(node).addClass('active');
                },
                "mouseout" : function ( event, node ) {
                    $(node).removeClass('active');
                }
            },

            // Switch on/off page
            'a.order_hidden' : {
                "click" : function( event, node ) {
                    $(node).find('i').addClass('sfcms-icon-progress');
                    $.get($( node ).attr('href'), function (data) {
                        $( node ).replaceWith(data);
                    });
                    return false;
                }
            },

            '#structureWrapper a.edit' : {
                "click": function (event, node) {
                    event.preventDefault();
                    this.$editDialog.html(this.progressTpl).dialog('option', 'title', 'Редактировать страницу').dialog('open');
                    $.get($(node).attr('href')).then($.proxy(function (response) {
                        this.$editDialog
                            .dialog('close')
                            .html(response)
                            .dialog("option", "title", i18n('Edit page'))
                            .dialog('open');
                    }, this));
//                    return false;
                }
            },

            '#structureWrapper a.add' : {
                "click": function (event, node) {
                    event.preventDefault();
                    var buttons = this.$createDialog.dialog('option', 'buttons');
                    delete buttons['Сохранить'];
                    this.$createDialog.dialog('option', 'buttons', buttons).html(this.progressTpl).dialog('open');
                    $.get($(node).attr('href')).then($.proxy(function (response) {
                        buttons['Сохранить'] = $.proxy(dialogCreateSave, this);
                        this.$createDialog.dialog('close').dialog('option', 'buttons', buttons).html(response).dialog('open');
                    }, this));
                }
            },

            /**
             * Remove page
             * Warning before remove
             */
            'a.do_delete' : {
                "click": function (event, node) {
                    try {
                        if (confirm(i18n('The data will be lost. Do you really want to delete?'))) {
                            $.post( $(node).attr('href'), function ( result ) {
                                if ( ! result.error ) {
                                    $('li[data-id="'+result.id+'"]').remove();
                                }
                            },"json");
                        }
                    } catch (e) {
                        console.error( e );
                    }
                    return false;
                }
            }
        },

        $editDialog: null,

        $createDialog: null,

        "init" : function() {
            /**
             * Сортировка для структуры сайта
             */
            $('div.b-main-structure ul').sortable(this.structureSortSettings).disableSelection();

            this.$createDialog = $('<div id="dialogCreatePage"/>').appendTo('body').dialog(dialogCreate);
            this.$editDialog = $('<div id="dialogEditPage"/>').appendTo('body').dialog(dialogEdit);
        },

        /**
         * Sortable settings
         */
        "structureSortSettings" : {
            stop : function (event, ui) {
                var positions = [];
                $('>li', this).each(function (i) {
                    positions.push($(this).attr('data-id'));
                });
                $.post('/page/resort/', {'sort':positions}).fail(function( response ){
                    console.error( response );
                });
            }
        }
    };
});
