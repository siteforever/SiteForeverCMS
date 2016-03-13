/**
 * Admin application
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
requirejs.config({
    "baseUrl": "/static",
    "config": {
        "locale": "ru"
    },
    "paths": {
        "jquery": "lib/jquery/dist/jquery",
        "jquery-ui": "lib/jquery-ui/jquery-ui",
        "jquery-form": "lib/jquery-form/jquery.form",
        "jquery-blockui": "lib/blockui/index",
        "fancybox": "lib/fancybox/source",
        "backbone": "lib/backbone/backbone",
        "underscore": "lib/underscore/underscore",
        "bootstrap": "lib/bootstrap/dist/js/bootstrap",
        "jstree": "lib/jstree/dist/jstree",
        "twig": "lib/twig.js/twig",
        "text": "lib/requirejs-text/text",
        "jqgrid": "lib/jqGrid/js",
        "chosen": "lib/chosen/chosen.jquery.min",
        "model-binder": "lib/backbone.model_binder/Backbone.ModelBinder",
        "theme": "/themes/bootstrap",
        "i18n": "i18n/ru",
        "app": "admin",
        "admin/jquery/elfinder/elfinder": "admin/jquery/elfinder/elfinder.min",
        "ckeditor": "lib/ckeditor"
    },
    "shim": {
        "backbone": { deps: ["underscore", "jquery"], exports: "Backbone" },
        "underscore":     { deps: [], exports: "_" },
        "bootstrap":      { deps: ["jquery"] },
        "backform":      { deps: ["underscore", "backbone"] },
        "jquery-ui":    { deps: ["jquery"] },
        "jquery-blockui": { deps: ["jquery"] },
        "jquery-form":    { deps: ["jquery"] },
        "jstree":         { deps: ["jquery"] },
        "fancybox/jquery.fancybox": { deps: ["jquery"] },
        "fancybox/helpers/jquery.fancybox-buttons": { deps: ["fancybox/jquery.fancybox"] },
        "fancybox/helpers/jquery.fancybox-media": { deps: ["fancybox/jquery.fancybox"] },
        "fancybox/helpers/jquery.fancybox-thumbs": { deps: ["fancybox/jquery.fancybox"] },
        "ckeditor/adapters/jquery": [
            "ckeditor/ckeditor"
        ]
    },
    "map": {
        "*": {
            "wysiwyg": "system/editor/ckeditor"
        }
    }
});

require([
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
    "jquery-ui",
    "system/admin/jquery/jquery.filemanager",
    "system/admin/jquery/jquery.dumper",
    "system/admin/log",
    "banner/admin/banner",
    "catalog/admin/catalog",
    "catalog/admin/goods",
    "catalog/admin/prodtype",
    "catalog/admin/catalogcomment",
    "dashboard/admin/dashboard",
    "elfinder/admin/elfinder",
    "gallery/admin/admin",
    "guestbook/admin/guestbook",
    "market/admin/delivery",
    "market/admin/manufacturers",
    "market/admin/material",
    "market/admin/order",
    "market/admin/payment",
    "news/admin/news",
    "page/admin/page",
    "user/admin/user"
], function(require, $, Backbone, i18n, module, behavior){

    window.lang = document.getElementsByTagName('html')[0].lang;

    $(document).ajaxError(function(event, jqxhr, settings, exception){
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
        errorAjaxDialog.html(jqxhr.responseText)
            .dialog('option', 'width', $(window).width() - 50)
            .dialog('option', 'height', $(window).height() - 50)
            .dialog('open');
    });

    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: '&#x3C;Пред',
        nextText: 'След&#x3E;',
        currentText: 'Сегодня',
        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
            'Июл','Авг','Сен','Окт','Ноя','Дек'],
        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        weekHeader: 'Нед',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['ru']);

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

    //var useController = module.config().use_controller || false;
    var useController = window.use_controller || false;
    window.controller = window.controller || "page/admin/page";
    console.log(useController, window.controller);

    $(document).ready(function(){
        if (useController) {
            var controller = require(window.controller);
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
            var DataGrig = require('system/view/DataGrid');
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

    /** Placeholder while initialisation */
    $('#loading-application').each(function(){
        $(this).fadeOut(200, function(){
            $(this).remove();
        });
    });

    console.log('work!');
});
