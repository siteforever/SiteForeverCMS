<?php
/**
 * @author: keltanas
 */
class form_Date extends form_Field
{
    protected $type = 'text';
    protected $class = 'datepicker';


    function __construct( $form, $name, $params )
    {
        parent::__construct( $form, $name, $params );
        if ( ! $this->value ) {
            $this->value = time();
        }
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return bool
     */
    function checkValue( $value )
    {
        return preg_match( '/\d{2}\.\d{2}\.\d{4}/', $value ) || preg_match( '/\d+/', $value );
    }

    /**
     * Вернет значение в виде строки
     * @return string
     */
    function getStringValue()
    {
        return strftime( '%x', $this->value );
    }


    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return bool
     */
    function setValue( $value )
    {
        if ( $this->checkValue( $value ) )
        {
            if ( preg_match( '/\d{2}\.\d{2}\.\d{4}/', $value ) ) {
                $this->value    = strtotime( $value );
            }
            else {
                $this->value  = $value;
            }
        }
        return $this;
    }




    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        if ( $this->readonly ) {
            $field['class']['class'] = 'date';
        }
        //printVar($field);

        return parent::doInput( $field );
    }

}
