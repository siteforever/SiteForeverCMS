<?php
/**
 * Поле для списка выбора select
 *
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Select extends Composite
{
    protected
        $class  = 'select';


    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $html = array();

        $multiple = in_array('multiple', $this->options) ? ' multiple="multiple" ' : '';
        $size     = isset( $this->options['size'] ) ? " size='{$this->options['size']}' " : '';


        $field['class'] = 'class="'.join(' ', $field['class']).'"';

        $html[] = "<select {$field['id']} {$field['class']} {$field['name']}{$multiple}{$size}>\n";
        if ( isset($this->options['variants']) )
        {
            foreach( $this->options['variants'] as $value => $label )
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
            return preg_match($this->filter, $value);
        }
    }
}

