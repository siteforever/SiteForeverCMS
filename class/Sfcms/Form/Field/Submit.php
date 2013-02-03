<?php
/**
 * Поле отправки данных
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field;

class Submit extends Field
{
    protected $_class    = 'submit';


    /**
     * Вернет HTML для поля
     * @param array $field
     * @return string
     */
    public function htmlInput( $field )
    {
        return "<input {$field['id']} type='submit' class='btn' {$field['value']} />";
    }

}