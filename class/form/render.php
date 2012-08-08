<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Form_Render extends Form_Abstract
{
    public function htmlStart()
    {
        $property = array();
        $property['action'] = $this->_action;
        $property['class'] = $this->_class;
        $property['enctype'] = 'multipart/form-data';
        $property['id'] = 'form_'.$this->_name;
        $property['method'] = $this->_method;
        $property['name'] = 'form_'.$this->_name;

        $html = array('<form');
        foreach ( $property as $key => $prop ) {
            $html[] = $key.'="'.$prop.'"';
        }
        $html[] = '>';
        return join(' ', $html);
    }

    public function htmlEnd()
    {
        return '</form>';
    }

    public function htmlFieldWrapped( $name )
    {
        return $this->getField( $name )->html();
    }

    public function htmlFieldLabel( $name )
    {
        return $this->getField( $name )->htmlLabel();
    }

    public function htmlField( $name )
    {
        return $this->getField( $name )->htmlField();
    }

    /**
     * html - код формы
     * @param $hint
     * @param $buttons
     * @return string
     */
    public function html( $hint = true, $buttons = true )
    {
        $html     = array();

        $html[]   = $this->htmlStart();

        foreach ( $this->_fields as $field ) {
            /** @var $field Form_Field */
            if ( is_object( $field ) ) {
                $html[ ] = $field->html();
            }
        }

        if ( $buttons && is_array( $this->_buttons ) ) {
            //$html[] = '<hr />';
            foreach ( $this->_buttons as $button ) {
                /** @var $button Form_Field */
                $html[]  = $button->html();
            }
        }

        if ( $hint ) {
            $html[]   = "<p><b>*</b> - поля, отмеченные звездочкой обязательны для заполнения</p>";
        }
        $html[]   = $this->htmlEnd();

        return join("\n", $html);
    }

}
