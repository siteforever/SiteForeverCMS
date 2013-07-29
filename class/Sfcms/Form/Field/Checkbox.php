<?php
/**
 * Чекбоксы
 * @author keltanas
 *
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field\Radio;
use Sfcms\Form\Field;

class Checkbox extends Field
{
    protected
        $_type   = 'checkbox',
        $_class  = 'checkbox';


    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $field['type'] = 'type="hidden"';
        $field['value'] = 'value="0"';
        unset($field['class']);
        $id = $field['id'];
        unset($field['id']);
        $result = "<input " . join(' ', $field) . " />";

        $field['id'] = $id;
        $field['value'] = 'value="1"';
        $field['type'] = 'type="checkbox"';
        if ($this->_value) {
            $field['checked'] = 'checked="checked"';
        }

        return  $result . "<input " . join(' ', $field) . " />";
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    public function checkValue( $value )
    {
        if ( is_array( $value ) ) {
            $check = true;
            foreach( $value as $val ) {
                $check &= $this->checkValue( $val );
            }
            return $check;
        }
        else {
            return preg_match($this->_filter, $value);
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
    function setValue( $value )
    {
        if ( ! is_array($value) && strpos( $value, ',' ) !== false ) {
            $value  = explode( ',', $value );
        }

        if ( $this->checkValue( $value ) )
        {
            $this->_value  = $value;
        }
        return $this;
    }


    function getStringValue()
    {
        if ( is_array($this->_value) ) {
            return join(',', $this->_value);
        }
        return $this->_value;
    }


}
