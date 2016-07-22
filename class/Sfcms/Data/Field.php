<?php
/**
 * Поле таблицы
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data;

use Sfcms\Data\Field\Blob;
use Sfcms\Data\Field\Datetime;
use Sfcms\Data\Field\Decimal;
use Sfcms\Data\Field\Int;
use Sfcms\Data\Field\Text;
use Sfcms\Data\Field\Tinyint;
use Sfcms\Data\Field\Varchar;

abstract class Field
{
    protected $name;
    protected $length;
    protected $null;
    protected $default;
    protected $autoincrement;

    public static $types = [
        Datetime::class => 'datetime',
        Decimal::class => 'decimal',
        Int::class => 'integer',
        Tinyint::class => 'integer',
        Text::class => 'text',
        Blob::class => 'blob',
        Varchar::class => 'string',
    ];

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
        if (null === $this->length) {
            $this->length = $length;
        }
        $this->null     = ! $notnull;
        $this->default  = $default;
        $this->autoincrement    = $autoincrement;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param null|string $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param boolean $null
     */
    public function setNull($null)
    {
        $this->null = $null;
    }

    /**
     * @return boolean
     */
    public function isNull()
    {
        return $this->null;
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

