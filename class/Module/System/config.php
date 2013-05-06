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
        'generator' => array(),
        'log'       => array('class'=>'Module\\System\\Controller\\LogController'),
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
        'Search'    => 'Module\\System\\Model\\SessionModel',
        'Settings'  => 'Module\\System\\Model\\SettingsModel',
        'Session'   => 'Module\\System\\Model\\SessionModel',
        'Templates' => 'Module\\System\\Model\\TemplatesModel',
        'User'      => 'Module\\System\\Model\\UserModel',
        'Log'       => 'Module\\System\\Model\\LogModel',
    ),
);