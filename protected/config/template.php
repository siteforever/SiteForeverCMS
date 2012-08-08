<?php
/**
 * Template settings
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
return array(
    'theme'     => 'basic',
    // драйвер шаблонизатора
    // это класс, поддерживающий интерфейс TPL_Driver
    'driver'    => 'TPL_Smarty',
    'version'   => '3.1.8',
    'widgets'   => SF_PATH.DIRECTORY_SEPARATOR.'widgets',
    'ext'       => 'tpl', // расширение шаблонов
    'admin'     => SF_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'system', // каталог шаблонов админки
    '404'       => 'error404', // шаблон страницы 404
);