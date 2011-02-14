<?php
/**
 * Поле для списка выбора select
 *
 * @author keltanas
 */
class Form_Field_Select extends Form_Field
{
    protected
        $type   = 'select',
        $class  = 'select';


    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
    	$html = array();

        $multiple = in_array('multiple', $this->params) ? ' multiple="multiple" ' : '';
        $size     = isset( $this->params['size'] ) ? " size='{$this->params['size']}' " : '';


        $field['class'] = 'class="'.join(' ', $field['class']).'"';

        $html[] = "<select {$field['id']} {$field['class']} {$field['name']}{$multiple}{$size}>\n";
    	if ( isset($this->params['variants']) )
    	{
            foreach( $this->params['variants'] as $value => $label )
            {
                $field['id']       = " id='{$this->getId()}_{$value}' ";
                $field['value']    = " value='{$value}' ";
                $field['selected'] = ( $this->value == $value ) ? " selected='selected' " : '';

                $html[]  = "<option {$field['value']} {$field['selected']}>{$label}</option>\n";
            }
    	}
        $html[] = "</select>\n";
        return join("\n", $html);
    }


    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return bool
     */
    function checkValue( $value )
    {
        //reg::getRequest()->addFeedback($this->name.' => '.$value);
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
}

