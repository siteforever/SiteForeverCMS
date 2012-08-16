(function($,$s){
    /**
     * Twitter bootstrap modal controller
     * @param id
     * @return {Boolean}
     * @constructor
     */
    $s.Modal = function( id ) {
        this._id = id;
        if ( ! $('#'+id).length ) {
            console.error('twModal "#'+id+'" not found');
            return false;
        }
        this.domnode = $('#'+id);
        this.domnode.on('shown', function(){
            wysiwyg.init();
        });
    };

    $s.Modal.prototype.onSave = function( callback ) {
        this.domnode.find('a.save').off('click');
        if ( typeof callback != 'function' ) {
            return false;
        }
        this._onSave = callback;
        this.domnode.find('a.save').on('click', $.proxy( function(){
            this._onSave.call(this);
            return false;
        }, this ));
    }

    $s.Modal.prototype.title = function( title ) {
        if ( title ) {
            this._title = title;
            this.domnode.find('.modal-header').find('h3').text( this._title );
            return this;
        } else {
            return this._title;
        }
    };

    $s.Modal.prototype.body = function( body ) {
        if ( body ) {
            this._body = body;
            this.domnode.find('.modal-body').html( this._body );
            return this;
        } else {
            return this._body;
        }
    };

    /**
     * Добавит сообщение в диалог.
     * @param msg
     * @param timeout
     * @return {*Promise}
     */
    $s.Modal.prototype.msgSuccess = function( msg, timeout ) {
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
    };

    /**
     *  Добавит ошибку в диалог
     * @param msg
     * @return {*Promise}
     */
    $s.Modal.prototype.msgError = function( msg ) {
        var deferred = new $.Deferred();
        $( '.modal-body', this.domnode).find('.alert').remove().end()
            .prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>'+msg+'</div>');
        deferred.resolve();
        return deferred.promise();
    };

    $s.Modal.prototype.show = function() {
        this.domnode.modal('show');
        return this;
    };

    $s.Modal.prototype.hide = function() {
        this.domnode.modal('hide');
        return this;
    };

})(jQuery,siteforever);
