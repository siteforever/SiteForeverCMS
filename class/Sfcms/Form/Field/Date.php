<?php
/**
 * @author: keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Exception;
use Sfcms\Form\FormFieldAbstract;
use Symfony\Component\Validator\Constraints\DateTime;

class Date extends FormFieldAbstract
{
    const FILTER_DATE_VALID_VALUES = '/^(?:\d{2}\.\d{2}\.\d{4}|\d+)$/';

    protected $type = 'text';
    protected $class = 'datepicker';

    protected $value   = 0;

    public function __construct($params)
    {
        parent::__construct($params);
        $this->filter = self::FILTER_DATE_VALID_VALUES;
        if (! $this->value) {
            $this->value = time();
        }
    }

    /**
     * Вернет значение в виде строки
     * @throws Exception
     * @return string
     */
    public function getStringValue()
    {
        $format = 'd.m.Y';
        if (preg_match('/^\d+$/', $this->value)) {
            return date($format, $this->value);
        } elseif ($this->value instanceof \DateTime) {
            return $this->value->format($format);
        }
        throw new Exception('Unknown value type for Date field ' . $this->value);
    }

    protected function checkValue($value)
    {
        if ($value instanceof \DateTime) {
            return true;
        }
        if (preg_match(self::FILTER_DATE_VALID_VALUES, $value)) {
            return true;
        }
        return false;
    }

    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     * @param $value
     * @return FormFieldAbstract|Date
     */
    public function setValue($value)
    {
        if ($this->checkValue($value)) {
            if ($value instanceof \DateTime) {
                $this->value = $value->getTimestamp();
            } elseif (preg_match('/^\d+$/', $value)) {
                $this->value = $value;
            } else {
                $this->value = strtotime($value);
            }
        }
        return $this;
    }
}
