<?php
/**
 * Конфиг
 * @author: keltanas
 * @link  http://siteforever.ru
 */

return array(
    'controllers' => array(
        'captcha'   => array(),
        'elfinder'  => array(),
        'error'     => array(),
        'feedback'  => array(),
        'generator' => array(),
        'routes'    => array(),
        'search'    => array(),
        'sitemap'   => array(),
        'system'    => array(),
        'users'     => array(),
        'setting'   => array(),
    ),
    'models'      => array(
        'Comments'  => 'Module\\System\\Model\\CommentsModel',
        'Module'    => 'Module\\System\\Model\\ModuleModel',
        'Routes'    => 'Module\\System\\Model\\RoutesModel',
        'Settings'  => 'Module\\System\\Model\\SettingsModel',
        'Templates' => 'Module\\System\\Model\\TemplatesModel',
        'User'      => 'Module\\System\\Model\\UserModel',
    ),
);