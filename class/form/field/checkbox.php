<?php
/**
 * Чекбоксы
 * @author keltanas
 *
 */
class Form_Field_Checkbox extends Form_Field_Radio
{
    protected
        $_type   = 'checkbox',
        $_class  = 'checkbox';


    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    function doInput( $field )
    {
        $html = array();

        if ( isset($this->_params['variants']) )
        {
            foreach( $this->_params['variants'] as $value => $label )
            {
                $field['id']     = "id='{$this->getId()}_{$value}'";
                $field['value']  = "value='{$value}'";

                $field['checked'] = '';
                if ( $this->_value == $value || ( is_array($this->_value) && in_array( $value, $this->_value) ) ) {
                    $field['checked'] = " checked='checked' ";
                }

                $field['name']   = "name='{$this->_form->name()}[{$this->_name}][{$value}]'";

                if ( is_array( $field['class'] ) ) {
                    $field['class'] = 'class="'.join(' ', $field['class']).'"';
                }

                $html[]  = "<input ".join(' ', $field)." /> <label for='{$this->getId()}_{$value}'>{$label}</label>";
            }
        }
        $br = in_array('br', $this->_params) ? "<br />" : "";
        return join($br."\n", $html);
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    function checkValue( $value )
    {
        if ( is_array( $value ) ) {
            $check = true;
            foreach( $value as $val ) {
                $check &= $this->checkValue( $val );
            }
            return $check;
        }
        else {
            return preg_match($this->_filter, $value);
        }
    }


    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return boolean
     */
    function setValue( $value )
    {
        if ( ! is_array($value) && strpos( $value, ',' ) !== false ) {
            $value  = explode( ',', $value );
        }

        if ( $this->checkValue( $value ) )
        {
            $this->_value  = $value;
        }
        return $this;
    }


    function getStringValue()
    {
        if ( is_array($this->_value) ) {
            return join(',', $this->_value);
        }
        return $this->_value;
    }


}