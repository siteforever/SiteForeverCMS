<?php
/**
 * Чекбоксы
 * @author keltanas
 *
 */
class form_Checkbox extends form_Radio
{
    protected
        $type   = 'checkbox',
        $class  = 'checkbox';


    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        $html = array();

        if ( isset($this->params['variants']) )
        {
            foreach( $this->params['variants'] as $value => $label )
            {
                $field['id']     = "id='{$this->getId()}_{$value}'";
                $field['value']  = "value='{$value}'";

                $field['checked'] = '';
                if ( $this->value == $value || ( is_array($this->value) && in_array( $value, $this->value) ) ) {
                    $field['checked'] = " checked='checked' ";
                }

                $field['name']   = "name='{$this->form->name()}[{$this->name}][{$value}]'";

                if ( is_array( $field['class'] ) ) {
                    $field['class'] = 'class="'.join(' ', $field['class']).'"';
                }

                $html[]  = "<input ".join(' ', $field)." /> <label for='{$this->getId()}_{$value}'>{$label}</label>";
            }
        }
        $br = in_array('br', $this->params) ? "<br />" : "";
        return join($br."\n", $html);
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return bool
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
            return preg_match($this->filter, $value);
        }
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
        if ( ! is_array($value) && strpos( $value, ',' ) !== false ) {
            $value  = explode( ',', $value );
        }

        if ( $this->checkValue( $value ) )
        {
            $this->value  = $value;
        }
        return $this;
    }


    function getStringValue()
    {
        if ( is_array($this->value) ) {
            return join(',', $this->value);
        }
        return $this->value;
    }


}