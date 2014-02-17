/**
 * Twitter bootstrap modal controller
 * @param id
 * @return {Boolean}
 * @constructor
 */

define('module/modal',[
    "jquery",
    "wysiwyg",
    "i18n",
    "module/alert",
    "twitter",
    "jui",
    "jquery/jquery.form",
    "admin/jquery/jquery.filemanager"
], function( $, wysiwyg, i18n, $alert ){

    var SfModal = function( id ) {
        this._id = id;
        if ( ! $('#'+id).length ) {
            $('body').append(this.template.replace(/\{\{id\}\}/,this._id));
        }
        this.domnode = $('#'+id);
        this.domnode.on('shown', function(){
            $('.datepicker').datepicker( window.datepicker );
            wysiwyg.init();
        });
        this.domnode.on('hidden', function(){
            if ( typeof wysiwyg.destroy == 'function' ) {
                wysiwyg.destroy();
            }
        });
        this.onSave( this.onSaveHandler );
    };

    SfModal.prototype = {

        template : '<div class="siteforeverModal modal hide" id="{{id}}" data-backdrop="static">'
            + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal">×</button>'
                + '<h3>{{title}}</h3>'
            + '</div>'
            + '<div class="modal-body">{{body}}</div>'
            + '<div class="modal-footer">'
                + '<a href="#" class="btn btn-primary save">' + i18n('Save changes') + '</a>'
                + '<a href="#" class="btn" data-dismiss="modal">' + i18n('Close') + '</a>'
            + '</div>'
        + '</div>'

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
                this.domnode.find('.modal-header').find('h3').text( this._title );
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
//            if ( timeout ) {
//                setTimeout($.proxy( function( deferred ) {
//                    this.hide.call(this);
//                    deferred.resolve();
//                },this, deferred), timeout);
//            } else {
//                deferred.resolve();
//            }
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
            this.domnode.modal('show');
            $('.modal-body', this.domnode).scrollTop(0);
            $('body').css('overflow', 'hidden');
            this.domnode.on("shown", function(){
                deferred.resolve();
                callback && callback();
            });
            return deferred.promise();
        }

        , hide : function(callback) {
            var deferred = new $.Deferred();
            this.domnode.modal('hide');
            this.domnode.on('hidden', function(){
                $('body').css('overflow', 'auto');
                deferred.resolve();
                callback && callback();
            });
            return deferred.promise();
        }
    };

    return SfModal;
});
