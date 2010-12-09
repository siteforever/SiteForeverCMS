/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    config.skin = 'v2';
	// Define changes to default configuration here. For example:
	config.language = 'ru';
	config.uiColor = '#EEEEEE';

    config.toolbar = 'Full';

    config.resize_enabled = false; // отключаем ресайз редактора
    config.entities = false;    // отключаем преобразование символов

    config.height   = '500';    // высота

    config.forcePasteAsPlainText = true;    // Всегда вставлять как текст

    config.startupOutlineBlocks = true; // показывать блоки
    config.templates_replaceContent = false;    // при добавлении шаблона не заменять текущее содержимое


    config.toolbar_Basic =
    [
        ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
    ];

    config.toolbar_Full =
            [
                ['Source','-','NewPage','Preview','-','Templates'],
                ['Paste','PasteText','PasteFromWord'],
                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                //['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
                ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                //'/',
                ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                ['Link','Unlink','Anchor'],
                ['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak'],
                ['Format'],
                ['TextColor','BGColor'],
                ['ShowBlocks','About']
            ];

};