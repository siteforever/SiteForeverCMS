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
    public function htmlInput( $field )
    {
        $value = $field['value'];
        unset( $field['value'] );

        $field['class'][] = 'input-xlarge';
        $field['class'] = "class='".join(' ', $field['class'])."'";
        return "<textarea ".join(' ', $field).">{$this->_value}</textarea>\n";
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
