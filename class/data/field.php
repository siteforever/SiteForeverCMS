<?php
/**
 * Поле таблицы
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
abstract class Data_Field
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
    function __construct( $name, $length = 11, $notnull = false, $default = null, $autoincrement = false )
    {
        $this->name     = $name;
        $this->length   = $length;
        $this->null     = ! $notnull;
        $this->default  = $default;
        $this->autoincrement    = $autoincrement;
    }

    function __toString()
    {
        return $this->toString();
    }

    /**
     * Имя поля
     * @return string
     */
    function getName()
    {
        return $this->name;
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
     * @return void
     */
    abstract function validate( $value );

}

