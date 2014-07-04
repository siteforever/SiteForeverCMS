/**
 * Basic file for administrative interface
 */
define("system/admin", [
    "require",
    "jquery",
    "backbone",
    "i18n",
    "module",
    "system/module/behavior",
    "system/module/alert",
    "system/view/WorkSpace",
    "system/view/DataGrid",
    "system/view/DataGridModal",
    "controller",
    "jquery-ui",
    "system/admin/jquery/jquery.filemanager",
    "system/admin/jquery/jquery.dumper",
    "datepicker_i18n"
],function(require, $, Backbone, i18n, module, behavior){

    window.lang = document.getElementsByTagName('html')[0].lang;

    $(document).ajaxError(function(event, xhr){
        var errorAjaxDialog = $('#errorAjaxDialog');
        if (0 == errorAjaxDialog.length) {
            errorAjaxDialog = $('<div id="errorAjaxDialog"></div>').appendTo('body');
            errorAjaxDialog.dialog({
                position: "center",
                title: "Ajax error",
                modal: true,
                autoOpen: false
            });
        }
        $.unblockUI();
        errorAjaxDialog.html(xhr.responseText)
            .dialog('option', 'width', $(window).width() - 50)
            .dialog('option', 'height', $(window).height() - 50)
            .dialog('open');
    });

//    Backbone.emulateHTTP = true;
    var dispatcher = {};
    _.extend(dispatcher, Backbone.Events);


    var ModalManager = Backbone.View.extend({
        windows: [],
        modal: null,
        dispatcher: null,

        initialize: function(options) {
            this.modal = options.modal;
            this.dispatcher = options.dispatcher;
        },

        create: function(opt) {
            opt = opt || {};
            var win = new this.modal(_.defaults(opt, {
                model: null,
                dispatcher: this.dispatcher
            }));
            win.id = _.uniqueId('modal');
            this.windows.push(win);
            return win;
        }
    });

    var winManager = new ModalManager({
        el: $('#workspace')[0],
        modal: require('system/view/DataGridModal'),
        dispatcher: dispatcher
    });

    var useController = module.config().use_controller || false;

    $(document).ready(function(){
        if (useController) {
            var controller = require('controller');
            if (typeof controller == 'function') { // it`s backbone
                new controller({
                    'el': $('#workspace')
                });
            } else {
                /** Run init */
                if (controller.init && typeof controller.init == "function") {
                    controller.init();
                }
                /** Apply behaviors */
                behavior.apply(controller);
            }
        } else {
            var DataGrig = require('misc/View/DataGrid');
            $('div.sfcms-admin-dataset').each(function(){
                new DataGrig({
                    el: $(this),
                    tplAdminItem: $("#tplAdminItem").html(),
                    tplAdminPagingItem: $('#tplAdminPagingItem').html(),
                    dispatcher: dispatcher,
                    winManager: winManager
                });
            });
        }

        $('a.dumper').dumper();
    });
});
