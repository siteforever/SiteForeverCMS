<?php
/**
 * Поле для списка выбора select
 *
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field;

class Select extends Composite
{
    protected
        $_class  = 'select';


    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $html = array();

        $multiple = in_array('multiple', $this->_params) ? ' multiple="multiple" ' : '';
        $size     = isset( $this->_params['size'] ) ? " size='{$this->_params['size']}' " : '';


        $field['class'] = 'class="'.join(' ', $field['class']).'"';

        $html[] = "<select {$field['id']} {$field['class']} {$field['name']}{$multiple}{$size}>\n";
        if ( isset($this->_params['variants']) )
        {
            foreach( $this->_params['variants'] as $value => $label )
            {
                $field['id']       = " id='{$this->getId()}_{$value}' ";
                $field['value']    = " value='{$value}' ";
                $field['selected'] = ( $this->_value == $value ) ? " selected='selected' " : '';

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
    public function checkValue( $value )
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
            return preg_match($this->_filter, $value);
        }
    }
}

