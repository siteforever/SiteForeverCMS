/**
 * Twitter bootstrap modal controller
 * @param id
 * @return {Boolean}
 * @constructor
 */

define('system/module/modal',[
    "jquery",
    "wysiwyg",
    "i18n",
    "system/module/alert",
    "bootstrap",
    "jquery-ui",
    "system/jquery/jquery.form",
    "system/admin/jquery/jquery.filemanager"
], function( $, wysiwyg, i18n, $alert ){

    var SfModal = function( id ) {
        this._id = id;
        this.domnode = $('#'+id);
        if ( ! this.domnode.length ) {
            this.domnode = $(this.template.replace(/\{\{id\}\}/,this._id)).appendTo('body');
        }
        this.domnode.on('shown.bs.modal', $.proxy(function(){
            this.init();
        }, this));
        this.domnode.on('hidden.bs.modal', $.proxy(function(){
            this.deInit();
        }, this));
        this.onSave( this.onSaveHandler );
    };

    SfModal.prototype = {

        template : '<div class="modal fade" id="{{id}}" data-backdrop="static">'
            + '<div class="modal-dialog modal-lg"><div class="modal-content">'
                + '<div class="modal-header">'
                    + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
                    + '<h3 class="modal-title">{{title}}</h3>'
                + '</div>'
                + '<div class="modal-body">{{body}}</div>'
                + '<div class="modal-footer">'
                    + '<a href="#" class="btn btn-primary save">' + i18n('Save changes') + '</a>'
                    + '<a href="#" class="btn btn-default" data-dismiss="modal">' + i18n('Close') + '</a>'
                + '</div>'
            + '</div></div>'
        + '</div>'

        , init: function() {
            $('.datepicker').datepicker( window.datepicker );
            wysiwyg.init();
            return this;
        }

        , deInit: function() {
            if ( typeof wysiwyg.destroy == 'function' ) {
                wysiwyg.destroy();
            }
            return this;
        }

        /**
         * Сохраняет обработчик сохранения
         * @param callback
         * @param args
         * @return {Boolean}
         */
        , onSave : function( callback, args ) {
            args = args || [];
            this.domnode.find('a.save').off('click');
            if ( typeof callback != 'function' ) {
                return false;
            }
            this._onSave = callback;
            this.domnode.find('a.save').on('click', $.proxy( function( args ){
                this._onSave.apply(this, args);
                return false;
            }, this, args ));
        }

       /**
        * Обработчик кнопки сохранения по умолчанию
        */
        , onSaveHandler : function(){
            $alert("Сохранение", $('.modal-body', this.domnode));
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function (response) {
                    if ( ! response.error ) {
                        this.msgSuccess(response.msg, 1500).done(function(){
                            window.location.reload();
                        });
                    } else {
                        this.msgError( response.msg );
                    }
                },this),
                'error': $.proxy(function (response){
                    alert(response);
                }, this)
            });
        }

        , title : function( title ) {
            if ( title ) {
                this._title = title;
                this.domnode.find('.modal-title').text( this._title );
                return this;
            } else {
                return this._title;
            }
        }

        , body : function( body ) {
            if ( body ) {
                this._body = body;
                this.domnode.find('.modal-body').html( this._body );
                return this;
            } else {
                return this._body;
            }
        }

        /**
         * Добавит сообщение в диалог.
         * @param msg
         * @param timeout
         * @return {*Promise}
         */
        , msgSuccess: function(msg, timeout) {
            var deferred = new $.Deferred();
            $alert(msg, timeout).done($.proxy(function(){
                this.hide.call(this);
                deferred.resolve();
            }, this));
            return deferred.promise();
        }

        /**
         *  Добавит ошибку в диалог
         * @param msg
         * @return {*Promise}
         */
        , msgError : function( msg ) {
            var deferred = new $.Deferred();
            $alert(msg, 1000, $('.modal-body', this.domnode));
            $('.modal-body', this.domnode).find('.alert').remove().end()
                .prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>'+msg+'</div>');
            deferred.resolve();
            return deferred.promise();
        }

        , show : function(callback) {
            var deferred = new $.Deferred();
            $('.modal-body', this.domnode).scrollTop(0);
            $('body').css('overflow', 'hidden');
            this.domnode.one("shown.bs.modal", function(){
                deferred.resolve();
                callback && callback();
            });
            this.domnode.modal('show');
            return deferred.promise();
        }

        , hide : function(callback) {
            var deferred = new $.Deferred();
            this.domnode.one('hidden.bs.modal', function(){
                $('body').css('overflow', 'auto');
                deferred.resolve();
                callback && callback();
            });
            this.domnode.modal('hide');
            return deferred.promise();
        }
    };

    return SfModal;
});
