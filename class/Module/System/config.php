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
        'system'    => array(),
        'setting'   => array(),
    ),
    'models'      => array(
        'Comments'  => 'Module\\System\\Model\\CommentsModel',
        'Module'    => 'Module\\System\\Model\\ModuleModel',
        'Routes'    => 'Module\\System\\Model\\RoutesModel',
        'Settings'  => 'Module\\System\\Model\\SettingsModel',
        'Session'   => 'Module\\System\\Model\\SessionModel',
        'Templates' => 'Module\\System\\Model\\TemplatesModel',
        'Log'       => 'Module\\System\\Model\\LogModel',
    ),
);
