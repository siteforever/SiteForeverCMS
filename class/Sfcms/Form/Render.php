<?php
namespace Sfcms\Form;

use Sfcms\Form\FormBaseAbstract;

/**
 *
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

class Render extends FormBaseAbstract
{
    public function htmlStart()
    {
        $property = array();
        $property['action'] = $this->action;
        $property['class'] = $this->class;
        $property['enctype'] = 'multipart/form-data';
        $property['id'] = 'form_'.$this->name;
        $property['method'] = $this->method;
        $property['name'] = 'form_'.$this->name;

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

    public function htmlFieldWrapped($name)
    {
        return $this->getChild($name)->html();
    }

    public function htmlFieldLabel($name)
    {
        return $this->getChild($name)->htmlLabel();
    }

    public function htmlField($name)
    {
        return $this->getChild($name)->htmlField();
    }

    public function htmlError($name)
    {
        return $this->getChild($name)->htmlError();
    }

    /**
     * html - код формы
     * @param $hint
     * @param $buttons
     * @return string
     */
    public function html($hint = true, $buttons = true)
    {
        $html     = array();

        $html[]   = $this->htmlStart();

        foreach ( $this->children as $field ) {
            /** @var $field FormFieldAbstract */
            if ( is_object( $field ) ) {
                $html[ ] = $field->html();
            }
        }

        if ( $buttons && is_array( $this->buttons ) ) {
            //$html[] = '<hr />';
            foreach ( $this->buttons as $button ) {
                /** @var $button FormFieldAbstract */
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
