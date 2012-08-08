<?php
/**
 * Интерфейс классов полей формы
 * @author keltanas <keltanas@gmail.com>
 *
 * Для полей формы рекомендуется переопределять следующие методы xxx
 */


abstract class Form_Field
{

    /**
     * Объект формы
     * @var Form_Form
     */
    protected $_form     = null;
    protected $_name     = '';
    protected $_value    = '';
    protected $_class    = 'text';
    protected $_id       = '';
    protected $_label    = '';
    protected $_notice   = '';

    protected $_filter   = '/.*/';

    protected $_readonly = false;
    protected $_required = false;
    protected $_disabled = false;
    protected $_hidden   = false;
    protected $_type      = null;
    protected $_params;

    /**
     * Есть ли у поля ошибка
     * @var boolean
     */
    protected   $_error = false;
    /**
     * Текст ошибки поля
     * @var string
     */
    protected   $_error_string  = '';
    
    /**
     * Создаем поле формы
     * @param Form_Form $form
     * @param string $name
     * @param array $params
     */
    public function __construct( Form_Form $form, $name, $params )
    {
        $this->_form = $form;
        $this->_name = $name;

        $this->_id       = $form->name().'_'.$name;

        if ( isset($params['class']) ) {
            $this->_class    = $params['class'];
        }

        if ( $this->in_array_strict('readonly', $params) ) {
            $this->_readonly = true;
        }

        if ( $this->in_array_strict('disable', $params) ) {
            $this->disable = true;
        }

        if ( $this->in_array_strict('hidden', $params) ) {
            $this->hide();
        }

        if ( $this->in_array_strict('required', $params) )
        {
            $this->setRequired();
        }

        if ( isset($params['label']) ) {
            $this->_label = $params['label'];
        }

        if ( isset($params['notice']) ) {
            $this->_notice = $params['notice'];
        }

        if ( isset( $params['value'] ) ) {
            if ( $this->setValue( trim( $params['value'] ) ) ) {
                if ( Form_Form::DEBUG ) {
                    print $this->_name.' = '.$params['value'].'<br />';
                }
            }
        }

        if ( isset( $params['filter'] ) ) {
            $this->_filter = $params['filter'];
        }

        $this->_params   = $params;
    }

    /**
     * Строгий поиск в массиве
     * @param mixed $val
     * @param array $array
     * @return boolean
     */
    protected function in_array_strict( $val, &$array )
    {
        foreach( $array as $arr ) {
            if ( $arr === $val ) {
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Вернет список настроек поля
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Вернет идентификатор поля
     * @return string
     */
    public function getId()
    {
        return $this->_form->name().'_'.$this->_name;
    }

    /**
     * Вернет наименование поля
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ( null === $this->_type ) {
            $this->_type    = strtolower( substr( get_class($this), strrpos( get_class($this), '_' ) + 1 ) );
        }
        return $this->_type;
    }

    /**
     * Скрыть поле
     */
    public function hide()
    {
        $this->_hidden   = true;
    }

    /**
     * Показать поле
     */
    public function show()
    {
        $this->_hidden   = false;
    }

    /**
     * Очистить
     */
    public function clear()
    {
        if ( isset( $this->_params['empty'] ) )
        {
            $this->_value    = $this->_params['empty'];
            return;
        }
        $this->_value    = '';
    }

    /**
     * Вернет значение поля
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Вернет значение в виде строки
     * @return string
     */
    public function getStringValue()
    {
        return (string) $this->_value;
    }

    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return Form_Field
     */
    public function setValue( $value )
    {
        if ( $this->checkValue( $value ) )
        {
            $this->_value  = $value;
        }
        return $this;
    }

    /**
     * Установить варианты выбора (для select и radio)
     * @param $list
     */
    public function setVariants( $list )
    {
        $this->_params['variants'] = $list;
    }

    /**
     * Назначать новую метку
     * @param $label
     */
    public function setLabel( $label )
    {
        $this->_label = $label;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    public function checkValue( $value )
    {
        return preg_match($this->_filter, $value);
    }

    /**
     * Проверка, является ли поле обязательным для заполнения
     * @return boolean
     */
    public function isRequired()
    {
        if ( ! $this->_hidden ) {
            return $this->_required;
        }
        return false;
    }

    /**
     * Устанавливает поле, как требуемое
     * @param boolean $required
     * @return void
     */
    public function setRequired( $required = true )
    {
        $this->_required = $required;
    }

    /**
     * Проверка, является ли значение поля "пустым"
     * @return boolean
     */
    public function isEmpty()
    {
        if ( isset( $this->_params['empty'] ) )
        {
            if ( $this->_params['empty'] == $this->_value )
            {
                return true;
            }
        }
        else {
            if ( empty( $this->_value ) )
            {
                return true;
            }
            if ( in_array( $this->getType(), array('text', 'textarea') ) && trim($this->_value) == '' )
            {
                return true;
            }
            if ( in_array( $this->getType(), array('int', 'float') ) && $this->_value == 0 )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверит значение поля на соответствие типу, а также заполнено ли
     * значение обязательного поля
     * @return boolean
     */
    public function validate()
    {
        $classes    = explode( ' ', trim($this->_class) );
        foreach ( $classes as $i => $class ) {
            if ( $class == 'error' ) {
                unset( $classes[ $i ] );
            }
        }

        // по умолчанию валидно
        $this->_error   = 0;

        if ( ! $this->checkValid() ) {
            $this->checkType();
        }

        if ( $this->_error > 0 ) {
            $this->_form->addFeedback( $this->_error_string );
            
            $classes[] = 'error';
            $this->_class    = join(' ', $classes);
        }

        return ! $this->_error;
    }

    /**
     * Проверка валидности
     * @return boolean
     */
    protected function checkValid()
    {
        if ( $this->isRequired() && $this->isEmpty() )
        {
            //    или если его значение пустое
            $this->_error   = 2;
            $this->_error_string    = "&laquo;{$this->_label}&raquo; нужно заполнить";
        }

        if ( ! $this->checkValue( $this->getValue() ) ) {
            $this->_error   = 3;
            $this->_error_string    = "&laquo;{$this->_label}&raquo; не соответсвует типу";
        }

        return ! $this->_error;
    }

    /**
     * Проверка типа
     * @return boolean
     */
    protected function checkType()
    {
        if ( ! ( $this->checkValue( $this->_value ) || $this->isEmpty() ) )
        {
            $this->_error   = 1;
            $this->_error_string    = "&laquo;{$this->_label}&raquo; не соответствует типу";
        }
        return ! $this->_error;
    }

    /**
     * Возвращает HTML для поля
     * @return string
     */
    public function html()
    {
        // если поле скрытое, то вывести скрытое
        if ( $this->_hidden ) {
            return $this->doInputHidden();
        }
        return $this->htmlWrapped();
    }

    /**
     * Декоратор полей
     * @return string
     */
    protected function htmlWrapped()
    {
        return "<div class='b-form-field'>"
                   .$this->htmlLabel()
                   ."<div class='b-form-field-{$this->getType()}'>"
                       .$this->htmlField().($this->_error ? "<div>{$this->_error_string}</div>" : '')
                   ."</div>"
                   .$this->htmlNotice()
                ."</div>";
    }


    public function htmlLabel()
    {
        return "<label for='{$this->getId()}'".($this->_error ? ' class="error"' : '').">"
            .$this->_label
            .( $this->isRequired() ? ' <b>*</b> ' : '' )
            ."</label>";
    }

    public function htmlNotice()
    {
        return $this->_notice ? "<div class=\"b-form-field-notice\"><small>{$this->_notice}</small></div>" : "";
    }

    public function htmlField()
    {
        $field = array();

        $field['id']  = "id='{$this->getId()}'";

        $field['type']     = "type='{$this->getType()}'";

        $class = explode( ' ', $this->_class );

        // исключения из типов
        if ( in_array( $this->getType(), array('int', 'float', 'date', 'password') ) )
        {
            $class['type'] = 'text';
        }

        if ( $this->isRequired() ) {
            $class['required']  = "required";
        }

        $field['class']    = $class;

        $field['name']     = "name='{$this->_form->name()}[{$this->_name}]'";
        $field['value']    = "value='{$this->getStringValue()}'";

        if ( $this->_readonly ) {
            $field['readonly'] = 'readonly="readonly"';
            $field['class']['readonly'] = 'readonly';
        }

        if ( $this->_disabled ) {
            $field['disabled'] = 'disabled="disabled"';
        }

        if ( isset($this->_params['autocomplete']) ) {
            $field['autocomplete']  = 'autocomplete="'.$this->_params['autocomplete'].'"';
        }
        return $this->htmlInput( $field );
    }

    /**
     * Вернет HTML для поля
     * @param $field
     * @return string
     */
    public function htmlInput( $field )
    {
        $field['class']    = 'class="'.join(' ', $field['class']).'"';
        return "<input ".join(' ', $field)." />";
    }

    /**
     * Вернет HTML для скрытого поля
     * @return string
     */
    protected function doInputHidden()
    {
        return "<input type='hidden' name='{$this->_form->name()}[{$this->_name}]' id='{$this->getId()}' value='{$this->_value}' />";
    }

    /**
     * @param $readonly
     */
    public function setReadonly( $readonly )
    {
        $this->_readonly = $readonly;
    }

    /**
     * @return boolean
     */
    public function getReadonly()
    {
        return $this->_readonly;
    }

    /**
     * @return boolean
     */
    public function getHidden()
    {
        return $this->_hidden;
    }
}