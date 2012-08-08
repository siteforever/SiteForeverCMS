/**
 * Basic file for administrative interface
 */
$(function () {
    $(':button, :submit, :reset, .button').button();

    // Подсветка разделов структуры
    $('div.b-main-structure span')
        .live('mouseover', function () {
            $(this).addClass('active');
        }).live('mouseout', function () {
            $(this).removeClass('active');
        });

    /**
     * Remove page
     * Warning before remove
     */
    $('a.do_delete').live('click',function () {
        if( confirm('Данные будут потеряны. Действительно хотите удалить?') ) {
            $.post( $( this ).attr('href') ).then( $.proxy( function(){
                $( this ).parent().parent().hide();
            }, this));
        }
        return false;
    });

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
     * Init dialogs
     */
    if ( 0 == $('#add_page_dialog' ).length ) {
        $('<div id="add_page_dialog"></div>' ).appendTo( 'body' ).dialog(sf.page.createDialog);
    }
    if ( 0 == $('#edit_page_dialog' ).length ) {
        $('<div id="edit_page_dialog"></div>' ).appendTo( 'body' ).dialog(sf.page.editDialog);
    }

    /**
     * Добавление нового раздела
     */
    $('div.b-main-structure a.add' ).live('click',function(){
        sf.page.a = this;
        $('#add_page_dialog' ).dialog('open');
        return false;
    });

    /**
     * Правка существующего раздела
     */
    $('div.b-main-structure a.edit' ).live('click',function(){
        sf.page.a = this;
        $.post( $(this).attr('href') ).then( $.proxy(function( response ){
            $('#edit_page_dialog' ).html( response ).dialog('option','title',sf.i18n('page','Edit page')).dialog('open');          //
        }, this));
        return false;
    });

    /**
     * Сортировка для структуры сайта
     */
    $('div.b-main-structure ul').sortable(sf.page.sortable).disableSelection();

    $('a.filemanager').filemanager();
    $('a.dumper').dumper();

    $('a.realias').realias();

    /**
     * По 2х щелчку открыть менеджер файлов
     */
    $('input.image').live('dblclick', $.fn.filemanager.input);
});





sf.page = {};

/**
 * Settings sortable plugin
 * @type {Object}
 */
sf.page.sortable = {
    stop : function (event, ui) {
        var positions = [];
        $('>li', this).each(function (i) {
            positions.push($(this).attr('this'));
        });
        $.post('/page/resort/', {'sort':positions} );
    }
};

/**
 * Кнопки диалога
 * @type {Object}
 */
sf.page.buttons = {};

/**
 * Кнопка создания при добавлении страницы
 * @type {Object}
 */
sf.page.buttons.createButton = {
    text: sf.i18n('Create'),
    click : function() {
        if ( ! $( '#name' ).val() ) {
            sf.alert(sf.i18n('Input Name'), 2000);
            return false;
        }

        // page/add
        $.post( $( '#url' ).val(), {
            'module':   $( '#module' ).val(),
            'name':     $( '#name' ).val(),
            'parent':   $( '#id' ).val()
        }).then( $.proxy( function( response ){
            $('#add_page_dialog' ).dialog('close');
            $('#edit_page_dialog' ).html( response ).dialog('option','title',sf.i18n('Create page')).dialog('open');
        }, this));
    }
};

/**
 * Конопка сохранения при редактировании страницы
 * @type {Object}
 */
sf.page.buttons.saveButton = {
    text: sf.i18n('Save'),
    click : function() {
        $( 'form', this ).ajaxSubmit({
            'dataType': 'json',
            'success' : $.proxy( function( response ) {
                sf.alert( response.error, 2000);
                if ( 0 == response.errno ) {
                    $(this ).dialog('close');
                    $.get('/page/admin' ).then(function( response ){
                        $('div.l-content-wrapper' ).html( response );
                        $('div.b-main-structure ul').sortable(sf.page.sortable).disableSelection();
                    });
                }
            }, this )
        });
    }
};

/**
 * Кнопка отмены при редактировании страницы
 * @type {Object}
 */
sf.page.buttons.cancelButton = {
    text: sf.i18n('Cancel'),
    click: function() {
        $(this ).dialog('close');
    }
};


/**
 * Dialog for create page
 * @type {Object}
 */
sf.page.createDialog = {
    'autoOpen':  false,
    'modal':     true,
    'resizable': false,
    'width':     300,
    'height':    200,
//    'top':       100,
//    'position': 'center',
    'open': function(){
        $(this ).html('Loading...');
        $( this ).dialog('option', 'title', $( sf.page.a ).attr('title'));
        // page/create
        $.post( $(sf.page.a ).attr('href'), {
            'id': $( sf.page.a ).attr('rel')
        } ).then( $.proxy(function( response ){
            $( this ).html( response );
        }, this));
    },
    'buttons' : [ sf.page.buttons.createButton, sf.page.buttons.cancelButton ]
};

/**
 * Dialog for edit page
 * @type {Object}
 */
sf.page.editDialog = {
    'autoOpen':  false,
    'modal':     true,
    'resizable': false,
    'width':     700,
//    'top':       100,
//    'position': 'center',
    'open'  :   function() {
        $( "#tabs" ).tabs();
        wysiwyg.init();
    },
    'buttons' : [ sf.page.buttons.saveButton, sf.page.buttons.cancelButton ]
};



