/**
 * Модуль управления страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define("page/admin/page", [
    "jquery",
    "backbone",
    "system/module/modal",
    "system/module/dialog",
    "i18n",
    "system/module/alert",
    "system/module/console",
    "wysiwyg",
    "jquery-ui",
    "system/jquery/jquery.form",
    "system/admin"
], function($, Backbone, Modal, Dialog, i18n, $alert, console, wysiwyg) {

    return Backbone.View.extend({
        "progressTpl": '<div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div>',

        editTitle: 'Редактировать страницу',

        editBody: '',

        "events" : {
            // Подсветка разделов структуры
            "mouseover div.b-main-structure li.tree-node>span": function(e){
                $(e.currentTarget).addClass('active');
            },

            "mouseout div.b-main-structure li.tree-node>span": function(e) {
                $(e.currentTarget).removeClass('active');
            },

            // Switch on/off page
            'click a.order_hidden': function(e) {
                $(e.target).find('i').addClass('sfcms-icon-progress');
                $.get($(e.target).attr('href'), function (data) {
                    $(e.target).replaceWith(data);
                });
                return false;
            },

            'click #structureWrapper a.edit': function (e) {
                e.preventDefault();
                this.editTitle = 'Редактировать страницу';
                this.editBody = this.progressTpl;
                this.showEdit();
                $.get($(e.target).attr('href')).then($.proxy(function (response) {
                    this.editBody = response;
                    this.editTitle = i18n('Edit page');
                    this.showEdit();
                }, this));
            },

            'click #structureWrapper button.btn-save': function(e){
                this.dialogEditSave();
            },

            'submit #structureWrapper form': function(e){
                e.preventDefault();
                this.dialogEditSave();
            },

            'click #structureWrapper button.btn-save-close': function(){
                this.dialogEditSave().always($.proxy(function(){
                    this.showStructure();
                }, this));
            },

            'click #structureWrapper button.btn-close': function() {
                this.showStructure();
            },

            'click #structureWrapper a.add': function(e) {
                e.preventDefault();
                var buttons = this.$createDialog.dialog('option', 'buttons');
                delete buttons['Сохранить'];
                this.$createDialog.dialog('option', 'buttons', buttons).html(this.progressTpl).dialog('open');
                $.get($(e.currentTarget).attr('href')).then($.proxy(function (response) {
                    buttons['Сохранить'] = $.proxy(this.dialogCreateSave, this);
                    this.$createDialog.dialog('close').dialog('option', 'buttons', buttons).html(response).dialog('open');
                }, this));
            },

            /**
             * Remove page
             * Warning before remove
             */
            'click a.do_delete': function(e) {
                try {
                    if (confirm(i18n('The data will be lost. Do you really want to delete?'))) {
                        $.post($(e.target).attr('href'), function (result) {
                            if (!result.error) {
                                $('li[data-id="' + result.id + '"]').remove();
                            }
                        }, "json");
                    }
                } catch (e) {
                    console.error(e);
                }
                return false;
            },

            "click button.btn-switch": function() {
                this.$el.find('div.b-main-structure').toggleClass('hide');
                this.$el.find('#pageEdit').toggleClass('hide');
            }
        },

        $editDialog: null,

        $createDialog: null,

        initialize : function() {
            /* Сортировка для структуры сайта */
            this.$el.find('div.b-main-structure').find('ul').sortable(this.structureSortSettings).disableSelection();
            this.$createDialog = $('<div id="dialogCreatePage"/>').appendTo('body').dialog(this.dialogCreate);
        },

        hideStructure: function() {
            this.$el.find('div.b-main-structure').addClass('hide');
            this.$el.find('#pageEdit').removeClass('hide');
        },

        showStructure: function() {
            this.$el.find('div.b-main-structure').removeClass('hide');
            this.closeEdit();
        },

        showEdit: function() {
            if (typeof wysiwyg.destroy == 'function') {
                wysiwyg.destroy();
            }
            var $pe = this.$el.find('#pageEdit');
            $pe.find('.title').html(this.editTitle);
            $pe.find('.body').html(this.editBody);
            $pe.find('.datepicker').datepicker();
            wysiwyg.init();
            this.hideStructure();
        },

        closeEdit: function() {
            this.$el.find('#pageEdit').addClass('hide');
            if (typeof wysiwyg.destroy == 'function') {
                wysiwyg.destroy();
            }
            this.$el.find('#pageEdit').find('.body').html('');
        },

        /**
         * Sortable settings
         */
        structureSortSettings: {
            stop : function (event, ui) {
                var positions = [];
                $('>li', this).each(function () {
                    positions.push($(this).attr('data-id'));
                });
                $.post('/page/resort/', {'sort':positions}).fail(function( response ){
                    console.error( response );
                });
            }
        },

        dialogEditSave: function(){
            var $form = $('form', this.$el),
                defer = $.Deferred(),
                timoutDefer = $.Deferred(),
                ajaxDefer = $.Deferred();
            $.blockUI({message: i18n('Saving'), fadeOut: 0});
            $form.ajaxSubmit({
                dataType:"json",
                success: $.proxy(function (response) {
                    if (!response.error) {
                        $.blockUI({message: response.msg});
                        setTimeout(function(){
                            $.unblockUI();
                            timoutDefer.resolve();
                        }, 1500);
                        $('div.b-main-structure').find('ul').sortable("destroy");
                        $.get('/page/admin' ).then($.proxy(function(response){
                            $('#structureWrapper').find('.b-main-structure').html($(response).find('.b-main-structure').html());
                            $('div.b-main-structure').find('ul').sortable(this.structureSortSettings).disableSelection();
                            ajaxDefer.resolve();
                        }, this));
                    } else {
                        $.unblockUI();
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

        dialogCreateSave: function(){
            var $name = $('#name');
            $name.parent().find('.help-inline').remove();
            if (!$.trim($name.val())) {
                $name.parent().append('<div class="help-inline">'+i18n('Input Name')+'</div>');
                $name.parents('.control-group').addClass('error');
                return;
            } else {
                $name.parents('.control-group').removeClass('error');
            }

            $.blockUI({'message': 'Отправка'});
            // page/add
            $.post($('#url').val(), {
                'module': $('#module').val(),
                'name': $name.val(),
                'parent': $('#id').val()
            }).then($.proxy(function (response) {
                this.$createDialog.unblock().html('').dialog('close');
                this.editTitle = i18n('Create page');
                this.editBody = response;
                this.showEdit();
                $.unblockUI();
            }, this));
        },

        dialogCreate: {
            title: 'Создать страницу',
            autoOpen: false,
            modal: true,
            width: 500,
            buttons: {
                "Закрыть": function() {
                    $(this).dialog('close');
                }
            }
        }
    });
});
