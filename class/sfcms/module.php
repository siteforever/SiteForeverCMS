<?php
/**
 * Модуль
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

abstract class Module
{
    /**
     * Вернет поле, которое связывает страницу с ее модулем
     * @static
     * @return string
     */
    public static function relatedField()
    {
        return 'link';
    }

    /**
     * Класс, который обозначает конкретный модуль
     * @static
     * @param $controller
     * @return string
     */
    public static function getModuleClass( $controller )
    {
        return '\\Modules\\'.ucfirst(strtolower($controller)).'\\Module';
    }

    /**
     * Название связывающей модели
     * @static
     * @return string
     */
    public static function relatedModel()
    {
        return 'Page';
    }

}
