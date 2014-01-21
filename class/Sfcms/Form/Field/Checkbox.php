<?php
/**
 * Чекбоксы
 * @author keltanas
 *
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field\Radio;
use Sfcms\Form\FormFieldAbstract;

class Checkbox extends FormFieldAbstract
{
    protected
        $type   = 'checkbox',
        $class  = 'checkbox';


    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    public function checkValue($value)
    {
        if (is_array($value)) {
            $check = true;
            foreach ($value as $val) {
                $check &= $this->checkValue($val);
            }
            return $check;
        }
        else {
            return preg_match('/[0|1]/', $value);
        }
    }


    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return boolean
     */
    public function setValue($value)
    {
        if (!is_array($value) && strpos($value, ',') !== false) {
            $value  = explode(',', $value);
        }

        if ($this->checkValue($value)) {
            $this->value = $value;
        }
        return $this;
    }


    public function getStringValue()
    {
        if (is_array($this->value)) {
            return join(',', $this->value);
        }
        return $this->value;
    }
}
