<?php
/**
 * Модуль пользователя
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
namespace Module\User;

use Sfcms\Model;
use Sfcms\Module as SfModule;

class Module extends SfModule
{
    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public function config()
    {
        return array(
            'controllers' => array(
                'user' => array(),
            ),
            'models'      => array(
                'User' => 'Module\\User\\Model\\UserModel',
            ),
        );
    }

}
