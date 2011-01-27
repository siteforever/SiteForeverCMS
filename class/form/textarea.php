<?php
/**
 * Поле многострочного поля
 * User: keltanas
 */
class form_Textarea extends form_Field
{
    protected $type = 'textarea';
    protected $class = 'textarea';
    protected $filter = '/.*/';

    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        $value = $field['value'];
        unset( $field['value'] );

        $field['class'] = "class='".join(' ', $field['class'])."'";
        return "<textarea ".join(' ', $field).">{$this->value}</textarea>\n";
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return bool
     */
    function checkValue( $value )
    {
        return true;
        //return preg_match($this->filter, $value);
    }
}
