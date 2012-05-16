<?php
/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Form_Render extends Form_Abstract
{
    /**
     * html - код формы
     * @param $hint
     * @param $buttons
     * @return string
     */
    public function html( $hint = true, $buttons = true )
    {
        $html     = array();

        $html[]   = "<form name='form_{$this->_name}' id='form_{$this->_name}' ".
                    "class='{$this->_class}' method='{$this->_method}' action='{$this->_action}' ".
                    "enctype='multipart/form-data'>";

        foreach ( $this->_fields as $field ) {
            /** @var $field Form_Field */
            if ( is_object( $field ) )
                $html[] = $field->html();
            elseif ( is_string( $field ) )
                $html[] = $field;
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
        $html[]   = "</form>";

        return join("\n", $html);
    }

}
