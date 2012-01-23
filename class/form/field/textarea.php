<?php
/**
 * Поле многострочного поля
 * User: keltanas
 */
class Form_Field_Textarea extends Form_Field
{
    protected $_class = 'textarea';
    protected $_filter = '/.*/';

    /**
     * Вернет HTML для поля
     * @var array $field
     * @return string
     */
    function doInput( $field )
    {
        $value = $field['value'];
        unset( $field['value'] );

        $field['class'] = "class='".join(' ', $field['class'])."'";
        return "<textarea ".join(' ', $field).">{$this->_value}</textarea>\n";
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    function checkValue( $value )
    {
        return true;
        //return preg_match($this->filter, $value);
    }
}
