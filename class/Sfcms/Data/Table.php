<?php
/**
 * Описание структуры данных
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

/**
 * 1. Инкапсулировать структуру таблицы
 * 2. Создать валидаторы для каждого поля
 *
 *
 */
namespace Sfcms\Data;

use Sfcms\Component;
use Sfcms\Data\AbstractDataField;

abstract class Table extends Component
{
    /**
     * Тип таблицы
     * @var string
     */
    //protected $engine   = 'MyISAM';
    protected static $engine   = 'InnoDB';

    /**
     * Список полей
     * @var array
     */
    protected static $fields = array();

    /**
     * Вернет имя таблицы
     * @return string
     */
    public function __toString()
    {
        return $this->app()->getConfig('db.prefix') . $this->table();
    }

    /**
     * @param string $engine
     */
    public static function setEngine($engine)
    {
        static::$engine = $engine;
    }

    /**
     * @return string
     */
    public static function getEngine()
    {
        return static::$engine;
    }

    /**
     * Вернет имя таблицы
     * @return string
     * @throws Exception
     */
    public static function table()
    {
        throw new Exception(sprintf('Need declare method in "%s::%s()"', get_called_class(), __FUNCTION__));
    }


    /**
     * Создаст список полей
     * @return array
     * @throws Exception
     */
    protected static function doFields()
    {
        throw new Exception(sprintf('Need declare method in "%s::%s()"', get_called_class(), __FUNCTION__));
    }

    /**
     * Вернет список полей
     * @abstract
     * @return array
     */
    public static function fields()
    {
        $class = get_called_class();
        if (!isset(static::$fields[$class])) {
            static::$fields[$class] = static::doFields();
        }

        return static::$fields[$class];
    }

    /**
     * Get field by name
     *
     * @param $name
     *
     * @return null|AbstractDataField
     */
    public function field($name)
    {
        $fields = static::fields();
        /** @var $field AbstractDataField */
        foreach ($fields as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Вернет первичный ключ
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    /**
     * Вернет список индексных ключей
     * @return array
     */
    public static function keys()
    {
        return array();
    }
}
