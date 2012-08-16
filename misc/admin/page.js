/**
 * Модуль управления страницами
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
(function($,$s){
    $(document).ready(function(){
        // Подсветка разделов структуры
        $('div.b-main-structure span')
            .live('mouseover', function () {
                $(this).addClass('active');
            }).live('mouseout', function () {
                $(this).removeClass('active');
            });

        var struntureSortSettings = {
            stop : function (event, ui) {
                var positions = [];
                $('>li', this).each(function (i) {
                    positions.push($(this).attr('data-id'));
                });
                $.post('/page/resort/', {'sort':positions}).fail(function( response ){
                    console.error( response );
                });
            }
        };

        /**
         * Сортировка для структуры сайта
         */
        $('div.b-main-structure ul').sortable(struntureSortSettings).disableSelection();

        /**
         * Switch on/off page
         */
        $('a.order_hidden').live('click', function () {
            var a = this;
            $.get($(a).attr('href'), function (data) {
                $(a).replaceWith(data);
            });
            return false;
        });


        /**
         * Подсветка таблицы
         */
        $('table.dataset tr').hover(function () {
            $(this).addClass('select');
        }, function () {
            $(this).removeClass('select');
        });

        /**
         * Create page dialog
         * @type {$s.Modal}
         */
        var createModal = new $s.Modal( 'pageCreate' );

        createModal.onSave(function(){
            if ( ! $.trim( $( '#name' ).val() ) ) {
                this.msgError( $s.i18n('page','Input Name') );
                return false;
            }

            // page/add
            $.post( $( '#url' ).val(), {
                'module':   $( '#module' ).val(),
                'name':     $( '#name' ).val(),
                'parent':   $( '#id' ).val()
            }).then( $.proxy( function( response ){
                this.hide();
                editModal.title($s.i18n('Create page')).body( response ).show();
            }, this));
        });

        $('#structureWrapper a.add').live('click', function(){
            $.post( $(this).attr('href') ).then( $.proxy(function( createModal, response ){
                createModal.title($(this).attr('title')).body( response ).show();
            }, this, createModal));
            return false;
        });


        /**
         * Edit page dialog
         * @type {$s.Modal}
         */
        var editModal = new $s.Modal( 'pageEdit' );

        editModal.onSave(function(){
            $('form', this.domnode).ajaxSubmit({
                dataType:"json",
                success: $.proxy(function( response ){
                    if ( ! response.error ) {
                        this.msgSuccess( response.msg, 1500).done(function(){
                            $.get('/page/admin' ).then(function( response ){
                                $('#structureWrapper').find('.b-main-structure').empty()
                                    .html( $( response ).find('.b-main-structure').html() );
                                $('div.b-main-structure ul').sortable(struntureSortSettings).disableSelection();
                            });
                        });
                    } else {
                        this.msgError( response.msg );
                    }
                },this)
            });
        });

        $('#structureWrapper a.edit').live('click', function(){
            $.post( $(this).attr('href') ).then( $.proxy(function( editModal, response ){
                editModal.title($s.i18n('page','Edit page')).body(response).show();
            }, this, editModal ));
            return false;
        });

        $('a.realias').realias();
    })
})(jQuery, siteforever);