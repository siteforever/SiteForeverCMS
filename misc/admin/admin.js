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
    window.datepicker = {
        dateFormat:'dd.mm.yy',
        firstDay:1,
        changeMonth:true,
        changeYear:true,
        showOn:'button'
    };
    if ('ru' == window.lang) {
        window.datepicker.dayNamesMin = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        window.datepicker.monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        window.datepicker.monthNamesShort = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
    }

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
        modal: require('View/AdminModal'),
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
            var DataGrig = require('View/DataGrid');
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
