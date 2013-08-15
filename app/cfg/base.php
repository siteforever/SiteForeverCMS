<?php
return array(
    'siteurl'   => 'http://'.$_SERVER['HTTP_HOST'],

//    'editor' => 'tinymce',
    'editor' => 'ckeditor',
//    'editor' => 'elrte',

    'language'  => 'ru',

    // база данных
    'db' => array(
        'dsn'       => 'mysql:host=localhost;dbname=siteforever',
        'login'     => 'root',
        'password'  => '',
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ),
        'migration' => false,
        'debug'     => false,
    ),

    // тема
    'template' => array(
        'theme'     => 'basic', // тема сайта
    ),

    'pager_template' => 'pager_twbt',

    'mailer_transport' => 'sendmail',

    'modules' => require_once __DIR__ . '/../modules.php',

);