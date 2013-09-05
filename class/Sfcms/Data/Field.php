<?php
/**
 * Поле таблицы
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data;

abstract class Field
{
    protected $name;
    protected $length;
    protected $null;
    protected $default;
    protected $autoincrement;


    /**
     * Создает поле
     * @param string $name
     * @param int $length
     * @param bool $notnull
     * @param string|null $default
     * @param bool $autoincrement
     */
    public function __construct($name, $length = 11, $notnull = false, $default = null, $autoincrement = false)
    {
        $this->name     = $name;
        $this->length   = $length;
        $this->null     = ! $notnull;
        $this->default  = $default;
        $this->autoincrement    = $autoincrement;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Имя поля
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement()
    {
        return $this->autoincrement;
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @abstract
     * @return string
     */
    abstract function toString();

    /**
     * Проверит значение на правильность
     * @abstract
     * @var mixed $value Значение
     * @return mixed
     */
    abstract function validate( $value );

}

