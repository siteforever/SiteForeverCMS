<?php
/**
 * Модуль
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Sfcms;

use App;
use Sfcms_Http_Exception;

abstract class Module
{
    protected $app;

    /** @param array */
    protected static $controllers = null;


    public function __construct()
    {
        $this->app = App::getInstance();
    }

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
        if ( null === self::$controllers ) {
            self::$controllers = App::getInstance()->getControllers();
        }
        if ( isset( self::$controllers[ $controller ] ) ) {
            $config = self::$controllers[ $controller ];
            return    '\\Module\\'
                    . ( isset( $config['module'] ) ? $config['module'] : ucfirst(strtolower($controller)) )
                    . '\\Module';
        }
//        throw new Sfcms_Http_Exception(sprintf('Contoroller %s not defined', $controller),404);
        throw new \RuntimeException(sprintf('Contoroller %s not defined', $controller),404);
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
