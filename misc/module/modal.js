/**
 * Twitter bootstrap modal controller
 * @param id
 * @return {Boolean}
 * @constructor
 */

define([
    "jquery",
    "wysiwyg",
    "siteforever",
    "twitter",
    "jui",
    "i18n",
    "jquery/jquery.form",
    "admin/jquery/jquery.filemanager"
], function( $, wysiwyg, $s ){

    var SfModal = function( id ) {
        this._id = id;
        if ( ! $('#'+id).length ) {
            $('body').append(this.template.replace(/\{\$id\}/,this._id));
        }
        this.domnode = $('#'+id);
        this.domnode.on('shown', function(){
            $('.datepicker').datepicker( $s.datepicker );
            $(document).on('dblclick','input.image',$.fn.filemanager.input);
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

        template : '<div class="siteforeverModal modal fade hide" id="{$id}">'
                    + '<div class="modal-header">'
                        + '<button type="button" class="close" data-dismiss="modal">×</button>'
                        + '<h3>{{title}}</h3>'
                    + '</div>'
                    + '<div class="modal-body">{{body}}</div>'
                    + '<div class="modal-footer">'
                        + '<a href="#" class="btn btn-primary save">' + $s.i18n('Save changes') + '</a>'
                        + '<a href="#" class="btn" data-dismiss="modal">' + $s.i18n('Close') + '</a>'
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
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500).done(function(){
                            window.location.reload();
                        });
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
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
        , msgSuccess : function( msg, timeout ) {
            var deferred = new $.Deferred();
            $( '.modal-body', this.domnode ).find('.alert').remove().end()
                .prepend('<div class="alert alert-success"><a class="close" data-dismiss="alert" href="#">×</a>'+msg+'</div>');
            if ( timeout ) {
                setTimeout($.proxy( function( deferred ) {
                    this.hide.call(this);
                    deferred.resolve();
                },this, deferred), timeout);
            } else {
                deferred.resolve();
            }
            return deferred.promise();
        }

        /**
         *  Добавит ошибку в диалог
         * @param msg
         * @return {*Promise}
         */
        , msgError : function( msg ) {
            var deferred = new $.Deferred();
            $( '.modal-body', this.domnode).find('.alert').remove().end()
                .prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>'+msg+'</div>');
            deferred.resolve();
            return deferred.promise();
        }

        , show : function() {
            this.domnode.modal('show');
            return this;
        }

        , hide : function() {
            this.domnode.modal('hide');
            return this;
        }
    };

    return SfModal;
});
