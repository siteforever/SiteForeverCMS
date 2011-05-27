<?php
/**
 * Радио-кнопки
 * @author keltanas
 *
 */
class Form_Field_Radio extends Form_Field_Composite
{
    protected
        $_type   = 'radio',
        $_class  = 'radio';

    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
    	$html = array();

    	if ( isset($this->_params['variants']) )
    	{
            foreach( $this->_params['variants'] as $value => $label )
            {
                $field['id']     = "id='{$this->getId()}_{$value}'";
                $field['value']  = "value='{$value}'";
                $field['checked']= ( $this->_value == $value ) ? " checked='checked' " : '';
                $field['class']  = 'class="radio"';//.join(' ', $field['class']).'"';

                $html[]  = "<input ".join(' ', $field)." /> <label for='{$this->getId()}_{$value}'>{$label}</label>";
            }
    	}
    	$br = in_array('br', $this->_params) ? "<br />" : "";
        return join($br."\n", $html);
    }
}