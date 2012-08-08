<?php
/**
 * @author: keltanas
 */
class Form_Field_Date extends Form_Field
{
    protected $_type = 'text';
    protected $_class = 'datepicker';

    protected $_value   = 0;

    public function __construct( $form, $name, $params )
    {
        parent::__construct( $form, $name, $params );
        if ( ! $this->_value ) {
            $this->_value = time();
        }
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    public function checkValue( $value )
    {
        return preg_match( '/\d{2}\.\d{2}\.\d{4}/', $value ) || preg_match( '/\d+/', $value );
    }

    /**
     * Вернет значение в виде строки
     * @return string
     */
    public function getStringValue()
    {
        return strftime( '%x', $this->_value );
    }


    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     * @param $value
     * @return Form_Field|Form_Field_Date
     */
    public function setValue( $value )
    {
        if ( $this->checkValue( $value ) ) {
            if ( preg_match( '/\d{2}\.\d{2}\.\d{4}/', $value ) ) {
                $this->_value    = strtotime( $value );
            } else {
                $this->_value  = $value;
            }
        }
        return $this;
    }




    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        if ( $this->_readonly ) {
            $field['class']['class'] = 'date';
        }
        return parent::htmlInput( $field );
    }

}
