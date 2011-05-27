<?php
/**
 * Поле отправки данных
 * @author keltanas
 */
class Form_Field_Submit extends Form_Field
{
    protected $_class    = 'submit';


    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        return "<input {$field['id']} type='submit' class='submit' {$field['value']} />";
    }

}