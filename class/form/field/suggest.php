<?php
/**
 * Поле выбора с предсказанием
 * @author keltanas
 */
class Form_Field_Suggest extends Form_Field
{
    protected $_type = 'text';
    protected $_class = 'xsuggest';

    function __construct( $form, $name, $params )
    {
        parent::__construct( $form, $name, $params );

        //$this->class .= " xsuggest";

        if ( empty( $params['field_value'] ) ) {
            $this->_form->addFeedback( "Укажите поле, куда сохранять значения 'field_value'" );
            exit();
        }
        if ( !isset( $params['ajax'] ) ) {
            $this->_form->addFeedback( "Укажите внешний файл данных 'ajax'" );
            exit();
        }
    }

    function doInput( &$field )
    {
        // выставляем статус OK
        if ( $this->_form->getField( $this->_params['field_value'] )->getValue() ) {
            $field['class'][] = "sug_ok";
        }
        //$field['class'][] = "progress";
        $field['ajax']      = "ajax='{$this->_params['ajax']}'";
        $field['field_id']  = "field_value='{$this->_params['field_value']}'";

        $script =   '<script type="text/javascript">'.
                    '$("#'.$this->_id.'").xsuggest();'.
                    '</script>';

        return parent::doInput( $field ).$script;
    }
}