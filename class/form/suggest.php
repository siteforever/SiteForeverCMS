<?php
/**
 * Поле выбора с предсказанием
 * @author keltanas
 */
class form_Suggest extends form_Field
{
    protected $type = 'text';
    protected $class = 'xsuggest';

    function __construct( $form, $name, $params )
    {
        parent::__construct( $form, $name, $params );

        //$this->class .= " xsuggest";

        if ( empty( $params['field_value'] ) ) {
            $this->form->addFeedback( "Укажите поле, куда сохранять значения 'field_value'" );
            exit();
        }
        if ( !isset( $params['ajax'] ) ) {
            $this->form->addFeedback( "Укажите внешний файл данных 'ajax'" );
            exit();
        }
    }

    function doInput( &$field )
    {
        // выставляем статус OK
        if ( $this->form->getField( $this->params['field_value'] )->getValue() ) {
            $field['class'][] = "sug_ok";
        }
        //$field['class'][] = "progress";
        $field['ajax']      = "ajax='{$this->params['ajax']}'";
        $field['field_id']  = "field_value='{$this->params['field_value']}'";

        $script =   '<script type="text/javascript">'.
                    '$("#'.$this->id.'").xsuggest();'.
                    '</script>';

        return parent::doInput( $field ).$script;
    }
}