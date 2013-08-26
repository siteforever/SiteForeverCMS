/**
 * Basic file for administrative interface
 */
define("admin/admin", [
    "require",
    "jquery",
    "backbone",
    "i18n",
    "module",
    "module/behavior",
    "module/alert",
    "View/WorkSpace",
    "View/DataGrid",
    "View/AdminModal",
    "controller",
    "siteforever",
    "jui",
    "admin/jquery/jquery.filemanager",
    "admin/jquery/jquery.dumper"
],function(require, $, Backbone, i18n, module, behavior, $alert){

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

    Backbone.emulateHTTP = true;
    var dispatcher = {};
    _.extend(dispatcher, Backbone.Events);


    var ModalManager = Backbone.View.extend({

        windows: [],

        initialize: function() {
        },

        create: function(opt) {
            opt = opt || {};
            var win = new this.options.modal(_.defaults(opt, {
                model: null,
                dispatcher: this.options.dispatcher
            }));
            win.id = _.uniqueId('modal');
            this.windows.push(win);
            return win;
        }
    });

    var winManager = new ModalManager({
        el: $('#workspace')[0],
        modal: require('View/AdminModal'),
        dispatcher: dispatcher
    });

    var useController = module.config().use_controller;

    $(document).ready(function(){
        if (useController) {
            var controller = require('controller');

            if (typeof controller == 'function') {
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

            var DataGrig = require('View/DataGrid');
            $('div.sfcms-admin-dataset').each(function(){
                var dataGrid = new DataGrig({
                    el: $(this),
                    tplAdminItem: $("#tplAdminItem").html(),
                    tplAdminPagingItem: $('#tplAdminPagingItem').html(),
                    dispatcher: dispatcher,
                    winManager: winManager
                });
                dataGrid.loadData();
            });
        }

        $('a.dumper').dumper();
    });
});
