/**
 * Модуль управления страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

define([
    "jquery",
    "siteforever",
    "module/modal",
    "i18n",
    "jui",
//    "admin/jquery/jquery.realias",
    "jquery/jquery.form",
    "admin/admin"
], function($, $s, Modal, i18n) {

    return {
        "behavior" : {
            // Подсветка разделов структуры
            'div.b-main-structure span' : {
                "mouseover" : function ( event, node ) {
                    $(node).addClass('active');
                },
                "mouseout" : function ( event, node ) {
                    $(node).removeClass('active');
                }
            },

            // Switch on/off page
            'a.order_hidden' : {
                "click" : function( event, node ) {
                    $.get($( node ).attr('href'), function (data) {
                        $( node ).replaceWith(data);
                    });
                    return false;
                }
            },

            '#structureWrapper a.edit' : {
                "click" : function( event, node ) {
                    $.post( $(node).attr('href') ).then( $.proxy(function( response ){
                        this.editModal.title(i18n('page','Edit page')).body(response).show();
                    }, this ));
                    return false;
                }
            },

            '#structureWrapper a.add' : {
                "click" : function( event, node ) {
                    $.post( $(node).attr('href') ).then( $.proxy(function( response ){
                        this.createModal.title( $(node).attr('title') ).body( response ).show();
                    }, this));
                    return false;
                }
            },

            /**
             * Remove page
             * Warning before remove
             */
            'a.do_delete' : {
                "click" : function ( event, node ) {
                    try {
                        if (confirm(i18n('The data will be lost. Do you really want to delete?'))) {
                            $.post( $(node).attr('href'), function ( result ) {
                                if ( ! result.error ) {
                                    $('li[data-id="'+result.id+'"]').remove();
                                    console.log( result );
                                }
                            },"json");
                        }
                    } catch (e) {
                        console.error( e );
                    }
                    return false;
                }
            }
        },


        "init" : function() {
            /**
             * Сортировка для структуры сайта
             */
            $('div.b-main-structure ul').sortable(this.structureSortSettings).disableSelection();

            /**
             * Edit page dialog
             * @type {$s.Modal}
             */
            this.editModal = new Modal( 'pageEdit' );
            this.editModal.onSave( this.editSave );

            /**
             * Create page dialog
             * @type {$s.Modal}
             */
            this.createModal = new Modal( 'pageCreate' );
            this.createModal.onSave( this.createSave, [this.editModal]);
        },

        /**
         * Sortable settings
         */
        "structureSortSettings" : {
            stop : function (event, ui) {
                var positions = [];
                $('>li', this).each(function (i) {
                    positions.push($(this).attr('data-id'));
                });
                $.post('/page/resort/', {'sort':positions}).fail(function( response ){
                    console.error( response );
                });
            }
        },

        /**
         * OnSave handler for create dialog
         */
        "createSave" : function( editModal ){
            if ( ! $.trim( $( '#name' ).val() ) ) {
                this.msgError( i18n('page','Input Name') );
                return;
            }
            // page/add
            $.post( $( '#url' ).val(), {
                'module':   $( '#module' ).val(),
                'name':     $( '#name' ).val(),
                'parent':   $( '#id' ).val()
            }).then( $.proxy( function( editModal, $s, response ){
                this.hide();
                editModal.title( i18n('page','Create page') ).body( response ).show();
            }, this, editModal, $s ) );
        },

        /**
         * OnSave handler for edit dialog
         */
        "editSave" : function(){
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy( function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500).done(function(){
                            $.get('/page/admin' ).then(function( response ){
                                $('#structureWrapper').find('.b-main-structure').empty()
                                    .html( $( response ).find('.b-main-structure').html() );
                                $('div.b-main-structure ul').sortable(this.struntureSortSettings).disableSelection();
                            });
                        });
                    } else {
                        this.msgError( response.msg );
                    }
                },this )
            });
        }
    };
});
