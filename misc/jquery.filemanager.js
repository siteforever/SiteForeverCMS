/**
 * Встроенный менеджер файлов для SiteForeverCMS
 * @author Ermin Nikolay <nikolay@ermin.ru>
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
$.fn.filemanager = function()
{
	$.fn.filemanager.path = '';

	$(this).click(function() {
		$.fn.filemanager.load({path:$.fn.filemanager.path});
		return false;
	});

	$('a.filemanager_new_catalog').live('click', function() {
		$.fn.filemanager.load({
			path		: $.fn.filemanager.path,
			new_dir		: $('#new_dir').val(),
			current_dir	: $('#current_dir').val()
		});
		return false;
	});

	$('#new_dir').live('keypress', function(event) {
		if ( event.charCode > 32 && String.fromCharCode(event.charCode).match(/[^\w\d\._-]+/g)  ) return false;
	});

    /* Менеджер файлов */
    $('a.filemanager_upload').live('click', function(){
        $(this).parents('form').ajaxSubmit({
        	target:'#filemanager_dialog',
        	success: function() {
        		$.fn.filemanager.opener();
        	}
		});
        return false;
    });


	$('a.filemanager_delete').live('click', function() {

		if ( confirm('Хотите удалить?') ) {
			$.fn.filemanager.load({"path":$.fn.filemanager.path, "delete":$(this).attr('delete')});
		}

		return false;
	});


	$('a[path]').live('click', function() {
		$.fn.filemanager.path	= $(this).attr('path');
		$.fn.filemanager.load({path:$.fn.filemanager.path});
		return false;
	});

	$.fn.filemanager.dialog = function( data )
	{
		if ( $('#filemanager_dialog').length == 0 )
		{
			$('body').append('<div id="filemanager_dialog"></div>');
			$('#filemanager_dialog').hide().dialog({
				autoOpen		: false,
				modal			: true,
				width			: 530,
				height			: 500,
				title			: 'Управление файлами'
			});
		}

		$('#filemanager_dialog').html(data).dialog('open');
		$.fn.filemanager.opener();
	};

	$.fn.filemanager.load = function( params )
	{
		$.fn.filemanager.dialog('Загрузка...');
		$.post('/admin/filemanager', params, function( data ){
			$.fn.filemanager.dialog(data);
		});
	};

	$.fn.filemanager.opener = function ()
	{
		if ( $('#ckeditor_browser').length ) {
			$('#filemanager_dialog a.gallery').click(function(){
				var funcNum = $('#CKEditorFuncNum').val();
				var fileUrl = $(this).attr('href');
				window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
				window.close();
				return false;
			});
		} else {
			$('#filemanager_dialog a.gallery').fancybox();
		}
	};
}