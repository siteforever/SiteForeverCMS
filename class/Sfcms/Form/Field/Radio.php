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
        $type   = 'radio',
        $class  = 'radio';

    /**
     * Вернет HTML для поля
     * @var array $filed
     * @return string
     */
    public function htmlInput( $field )
    {
        $html = array();

        if ( isset($this->options['variants']) )
        {
            foreach( $this->options['variants'] as $value => $label )
            {
                $field['id']     = "id='{$this->getId()}_{$value}'";
                $field['value']  = "value='{$value}'";
                $field['checked']= ( $this->value == $value ) ? " checked='checked' " : '';
                $field['class']  = 'class="btn"';//.join(' ', $field['class']).'"';

                $html[]  = "<label for='{$this->getId()}_{$value}' class='checkbox'>";
                $html[]  = "<input ".join(' ', $field).">";
                $html[]  = "{$label}</label>";
            }
        }
        $br = in_array('br', $this->options) ? "<br />" : "";
        return join($br."\n", $html);
    }

    /**
     * Вернет метку выбранного значения
     * @return string
     */
    public function getLabelOfValue()
    {
        return isset( $this->options['variants'][ $this->value ] )
            ? $this->options['variants'][ $this->value ] : '';
    }
}
