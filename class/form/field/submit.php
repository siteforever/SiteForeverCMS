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
     * @param array $field
     * @return string
     */
    public function htmlInput( $field )
    {
        return "<input {$field['id']} type='submit' class='submit' {$field['value']} />";
    }

}