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
    /** @var App */
    protected $app;

    /** @var string */
    protected $name;

    /** @var string */
    protected $path;

    /** @param array */
    protected static $controllers = null;


    public function __construct( App $app, $name, $path )
    {
        $this->app = $app;
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Должна вернуть массив конфига для модуля
     * @return mixed
     */
    public abstract function config();

    /**
     * Вернет поле, которое связывает страницу с ее модулем
     * @static
     * @return string
     */
    public static function relatedField()
    {
        return 'link';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
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
        print_r( self::$controllers );
//        die(sprintf('Contoroller %s not defined', $controller));
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
