/**
*	@author Nikolay Ermin
*
*	JavaScript приложение к модулю форм
*/
$(function() {
	// вводим uri
	$('#structure_uri,#structure_alias').bind('keypress', function(event) {
		if ( 	  event.keyCode == 8 || event.keyCode == 9 ||
				( event.keyCode >= 33 && event.keyCode <= 40 ) ||
				( event.keyCode >= 45 && event.keyCode <= 47 ) ||
				( event.charCode >= 47 && event.charCode <= 57 ) ||
				( event.charCode >= 95 && event.charCode <= 122 && event.charCode != 96 )
		) {	}
		else {
			event.preventDefault();
			return false;
		}
	});

	var editor = $('textarea').not('.plain').ckeditor({
        filebrowserBrowseUrl 		: '/?route=elfinder&finder=1',
        filebrowserImageBrowseUrl 	: '/?route=elfinder&finder=1',
        filebrowserWindowWidth : '530',
        filebrowserWindowHeight : '500',
        filebrowserImageWindowWidth : '530',
        filebrowserImageWindowHeight : '500'
	});

	// Добавляем окно для обработчика форм
	if ( $('#form_container').length == 0 ) {

		$('body').append("<div id='form_container' title='Сохраняем...'></div>");
				//.append("<iframe id='form_frame' name='form_frame'></iframe>");

		$('#form_container').hide().dialog({
			bgiframe: true,
			modal: true,
			autoOpen: false,
			width: 400,
			zindex: 100,
			draggable: true,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				},
        		"Обновить": function() {
				    window.location.reload(true);
				}
			},
			close: function() {
				$(this).html("");
			}
		});
	}

	// обработчик сабмита

    $('form.module_form, form.ajax').ajaxForm({
		//target:		"#form_container",
        beforeSubmit: function()
        {
            $.showBlock('Отправка данных...');
        },
		success:	function( data ) {
            $('div.blockMsg').html('').append( data );
            $.hideBlock(2000);
		},
		iframe:		false
	}).find("input:text").live('keypress', function(e){
		if( e.keyCode == 13 /*|| e.keyCode == 9*/ ) {
			return false;
		}
	});


	/*
	 * Цепляем элементы календаря
	 */
	$('.datepicker').datepicker({
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		changeMonth: true,
		changeYear: true,
		buttonImage: '/images/admin/icons/calendar.png',
		buttonImageOnly: true,
		showOn: 'button',
		dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
		     	  	'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
		      	  	'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
	});

	// обработка suggest
	/*$('input.xsuggest').xsuggest().bind("keypress", function(event){
		if ( event.keyCode == 13 ) {
			event.stopPropagation();
			return false;
		}
	});*/

	$(':reset').click(function(){
		$(this).parents("form").clearForm().submit();
		return false;
	});

});


// отображает блокировку
$.showBlock  = function( message ){
    $.blockUI({
        message: message,
        css: {
            border                  : 'none',
            padding                 : '15px',
            'font-size'             : '16px',
            backgroundColor         : '#000',
            '-webkit-border-radius' : '10px',
            '-moz-border-radius'    : '10px',
            'border-radius'         : '10px',
            opacity                 : .5,
            color                   : '#fff'
        }
    });
}

// скрывает блокировку
$.hideBlock  = function ( timeout ) {
    timeout = timeout || 0;
    if ( timeout ) {
        setTimeout($.unblockUI, timeout);
    } else {
        $.unblockUI();
    }
}

/**
 *	Функция создает из строки ?a=1&b=2 массив {"a":"1","b":"2"}
 */
var requestSplit = function ( request ) {
	var data = {};
	for ( var val in request.replace( '?', '' ).split('&') ) {
		var a = val.split('=');
		data[ a[0] ] = a[1];
	}
	/*$.each( request.replace( '?', '' ).split('&'), function( key, val ){
		var a = val.split('=');
		data[ a[0] ] = a[1];
	});*/
	return data;
}