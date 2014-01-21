<?php
/**
 * Поле выбора с предсказанием
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;

class Suggest extends FormFieldAbstract
{
    protected $type = 'text';
    protected $class = 'xsuggest';

    public function __construct( $form, $name, $params )
    {
        parent::__construct( $form, $name, $params );

        //$this->class .= " xsuggest";

        if ( empty( $params['field_value'] ) ) {
            $this->parent->addFeedback( "Укажите поле, куда сохранять значения 'field_value'" );
            exit();
        }
        if ( !isset( $params['ajax'] ) ) {
            $this->parent->addFeedback( "Укажите внешний файл данных 'ajax'" );
            exit();
        }
    }

    /**
     * @param $field
     * @return string
     */
    public function htmlInput( $field )
    {
        // выставляем статус OK
        if ( $this->parent->getField( $this->options['field_value'] )->getValue() ) {
            $field['class'][] = "sug_ok";
        }
        //$field['class'][] = "progress";
        $field['ajax']      = "ajax='{$this->options['ajax']}'";
        $field['field_id']  = "field_value='{$this->options['field_value']}'";

        $script =   '<script type="text/javascript">'.
                    '$("#'.$this->id.'").xsuggest();'.
                    '</script>';

        return parent::htmlInput( $field ).$script;
    }
}
