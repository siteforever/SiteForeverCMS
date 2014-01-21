<?php
/**
 * Поле многострочного поля
 * User: keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Textarea extends FormFieldAbstract
{
    protected $class = 'textarea input-xlarge';
    protected $filter = '/.*/';

    /**
     * Вернет HTML для поля
     * @var array $field
     * @return string
     */
    public function htmlInput( $field )
    {
        $value = $field['value'];
        unset( $field['value'] );

        $field['class'][] = 'input-xlarge';
        $field['class'] = "class='".join(' ', $field['class'])."'";
        return "<textarea ".join(' ', $field).">{$this->value}</textarea>\n";
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    public function checkValue( $value )
    {
        return true;
        //return preg_match($this->filter, $value);
    }
}
