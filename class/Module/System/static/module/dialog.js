/**
 * Модуль отвечающий за диалог
 * @author: keltanas
 * @link http://siteforever.ru
 */

define('system/module/dialog',[
    "jquery",
    "i18n",
    "system/module/alert",
    "jquery-ui",
    "system/jquery/jquery.form"
], function ($, i18n, $alert) {

    var dialog = function( /* string */ id, /* object */ obj ){
        id = id || 'sfcms_dialog';
        this.obj = obj;

        if (!$('#' + id).length) {
            var cfg = {
                resizable: false,
                autoOpen: false,
                height: $(window).height() - 50,
                width: 800,
                modal: true,
                overflow: '',
                open: function (event, ui) {
                    if ( obj.onOpen && typeof obj.onOpen == 'function' ) {
                        obj.onOpen.apply(obj);
                        $('body, html').css('overflow', 'hidden');
                    }
                },
                close: function (event, ui) {
                    if ( obj.onClose && typeof obj.onClose == 'function' ) {
                        obj.onClose.apply(obj);
                        $('body, html').css('overflow', 'visible');
                    }
                }
            };

            if (obj.dialogCfg && typeof obj.dialogCfg == 'object') {
                cfg = $.extend(true, cfg, obj.dialogCfg);
            }

            this.$dialog = $('<div id="'+id+'" title="{{ title }}"></div>').appendTo('body').hide().dialog(cfg);

            this.$dialog.dialog("option", "buttons",[
                {'text': i18n('Save'), 'click' :$.proxy(this.save,this)},
                {'text': i18n('Save & close'), 'click' :$.proxy(function() {
                    this.save().done( $.proxy( this.close, this ) );
                },this)},
                {'text': i18n('Close'), 'click' :$.proxy(this.close,this)}
            ]);
        }
    };

    dialog.prototype.title = function (title) {
        this.$dialog.dialog('option', 'title', title);
        return this;
    };

    dialog.prototype.body = function( html ) {
        this.$dialog.html( html );
        return this;
    };

    dialog.prototype.open = function() {
        this.$dialog.dialog('open');
        return this;
    };

    dialog.prototype.close = function() {
        this.$dialog.dialog('close');
        return this;
    };

    dialog.prototype.isOpen = function() {
        return this.$dialog.dialog('isOpen');
    };

    dialog.prototype.widget = function() {
        return this.$dialog.dialog('widget');
    };

    dialog.prototype.moveOnTop = function() {
        this.$dialog.dialog('moveOnTop');
        return this;
    };

    dialog.prototype.save = function(node) {
        var deferred = $.Deferred();
        $.blockUI({message:"Saving"});
        $('form', this.$dialog).ajaxSubmit({
            dataType: "json",
            success: $.proxy(function (response) {
                $.unblockUI();
                if (response.errors) {

                }
                if (!response.error && this.obj.onSave && typeof this.obj.onSave == 'function' ) {
                    deferred.done($.proxy(this.obj.onSave, this.obj, response));
                }
                // apply onSave
                $alert(response.msg, 1000, this.$dialog.parent())
                    .done(response.error ? deferred.reject : deferred.resolve);
            },this),
            error: function() {
                $.unblockUI();
            }
        });
        return deferred.promise();
    };

    return dialog;
});
