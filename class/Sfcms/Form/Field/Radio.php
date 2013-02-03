<?php
/**
 * Радио-кнопки
 * @author keltanas
 *
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field\Composite;

class Radio extends Composite
{
    protected
        $_type   = 'radio',
        $_class  = 'radio';

    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $html = array();

        if ( isset($this->_params['variants']) )
        {
            foreach( $this->_params['variants'] as $value => $label )
            {
                $field['id']     = "id='{$this->getId()}_{$value}'";
                $field['value']  = "value='{$value}'";
                $field['checked']= ( $this->_value == $value ) ? " checked='checked' " : '';
                $field['class']  = 'class="btn"';//.join(' ', $field['class']).'"';

                $html[]  = "<label for='{$this->getId()}_{$value}' class='checkbox inline'>";
                $html[]  = "<input ".join(' ', $field).">";
                $html[]  = "{$label}</label>";
            }
        }
        $br = in_array('br', $this->_params) ? "<br />" : "";
        return join($br."\n", $html);
    }

    /**
     * Вернет метку выбранного значения
     * @return string
     */
    public function getLabelOfValue()
    {
        return isset( $this->_params['variants'][ $this->_value ] )
            ? $this->_params['variants'][ $this->_value ] : '';
    }
}
