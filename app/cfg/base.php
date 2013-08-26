<?php
return array(
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],

//    'editor' => 'tinymce',
    'editor' => 'ckeditor',
//    'editor' => 'elrte',

    'language'  => 'ru',

    // тема
    'template' => array(
        'theme'     => 'basic', // тема сайта
    ),

    'pager_template' => 'pager_twbt',

    'mailer_transport' => 'sendmail',

    'modules' => require_once __DIR__ . '/../modules.php',

);
