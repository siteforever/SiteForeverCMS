<?php
/**
 * Радио-кнопки
 * @author keltanas
 *
 */
class form_Radio extends form_Field
{
    protected
        $type   = 'radio',
        $class  = 'radio';

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
                $field['checked']= ( $this->value == $value ) ? " checked='checked' " : '';
                $field['class']  = 'class="radio"';//.join(' ', $field['class']).'"';

                $html[]  = "<input ".join(' ', $field)." /> <label for='{$this->getId()}_{$value}'>{$label}</label>";
            }
    	}
    	$br = in_array('br', $this->params) ? "<br />" : "";
        return join($br."\n", $html);
    }
}