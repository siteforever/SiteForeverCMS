<?php
/**
 * Поле отправки данных
 * @author keltanas
 */
class form_Submit extends form_Field
{
    protected $type     = 'submit';
    protected $class    = 'submit';


    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        return "<input {$field['id']} type='submit' class='submit' {$field['value']} />";
    }

}