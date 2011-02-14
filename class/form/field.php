<?php
/**
 * @author keltanas <keltanas@gmail.com>
 * Интерфейс классов полей формы
 *
 * Для полей формы рекомендуется переопределять следующие методы xxx
 */

// todo В случае использования чекбоксов и радиобаттонов видимо надо реализовывать списки значений, как в селектах

abstract class Form_Field
{
    protected
            /**
             * Объект формы
             * @var form_Form
             */
            $form     = null,
            $name     = '',
            $value    = '',
            $class    = 'text',
            $id       = '',
            $label    = '',

            $filter   = '/.*/',

            $readonly = false,
            $required = false,
            $disabled = false,
            $hidden   = false,
            $params;

    /**
     * Создаем поле формы
     * @param  $form
     * @param  $name
     * @param  $params
     * @return void
     */
    function __construct( $form, $name, $params )
    {
        $this->form = $form;
        $this->name = $name;

        $this->id       = $form->name().'_'.$name;

        if ( isset($params['class']) ) {
            $this->class    = $params['class'];
        }

        if ( $this->in_array_strict('readonly', $params) ) {
            $this->readonly = true;
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
            $this->label = $params['label'];
        }

        if ( isset( $params['value'] ) ) {
            if ( $this->setValue( $params['value'] ) ) {
                if ( form_Form::DEBUG ) {
                    print $this->name.' = '.$params['value'].'<br />';
                }
            }
        }

        if ( isset( $params['filter'] ) ) {
            $this->filter = $params['filter'];
        }

        $this->params	= $params;
    }

    /**
     * Строгий поиск в массиве
     * @param mixed $val
     * @param array $array
     * @return bool
     */
    function in_array_strict( $val, &$array )
    {
        foreach( $array as $arr ) {
            if ( $arr === $val ) {
                return true;
            }
        }
    }

    function __toString()
    {
        return $this->getStringValue();
    }

    /**
     * Вернет список настроек поля
     * @return array
     */
    function getParams()
    {
        return $this->params;
    }

    /**
     * Вернет идентификатор поля
     */
    function getId()
    {
        return $this->form->name().'_'.$this->name;
    }

    /**
     * Вернет наименование поля
     */
    function getName()
    {
        return $this->name;
    }

    function getType()
    {
        return $this->type;
    }

    /**
     * Скрыть поле
     */
    function hide()
    {
        $this->hidden   = true;
    }

    /**
     * Показать поле
     */
    function show()
    {
        $this->hidden   = false;
    }

    /**
     * Очистить
     */
    function clear()
    {
        if ( isset( $this->params['empty'] ) )
        {
            $this->value    = $this->params['empty'];
            return;
        }
        $this->value    = '';
    }

    /**
     * Вернет значение поля
     * @return mixed
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * Вернет значение в виде строки
     * @return string
     */
    function getStringValue()
    {
        return $this->value;
    }

    /**
     * Установит значение поля, предварительно проверив его
     * Если значение не удовлетворяет типу поля, то оно не будет установлено,
     * а метод вернет false
     *
     * @param $value
     * @return form_Field
     */
    function setValue( $value )
    {
        if ( $this->checkValue( $value ) )
        {
            $this->value  = $value;
        }
        return $this;
    }

    /**
     * Установить варианты выбора (для select и radio)
     * @param $list
     */
    function setVariants( $list )
    {
        $this->params['variants'] = $list;
    }

    /**
     * Назначать новую метку
     * @param $label
     */
    function setLabel( $label )
    {
        $this->label = $label;
    }

    /**
     * Добавить варианты выбора к уже имеющимся (для select и radio)
     * @param $list
     */
    function addVariants( $list )
    {
        $this->params['variants'] = array_merge( $this->params['variants'], $list );
    }


    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return bool
     */
    function checkValue( $value )
    {
        //$this->form->addFeedback($this->name.' => '.$value);
        //print "preg_match('{$this->filter}', '{$value}')".@preg_match($this->filter, $value)."<br />";
        return preg_match($this->filter, $value);
    }

    /**
     * Проверка, является ли поле обязательным для заполнения
     * @return bool
     */
    function isRequired()
    {
        if ( ! $this->hidden ) {
            return $this->required;
        }
        return false;
    }

    /**
     * Устанавливает поле, как требуемое
     * @param bool $required
     * @return void
     */
    function setRequired( $required = true )
    {
        $this->required = $required;
    }

    /**
     * Проверка, является ли значение поля "пустым"
     * @return bool
     */
    function isEmpty()
    {
        if ( isset( $this->params['empty'] ) )
        {
            if ( $this->params['empty'] == $this->value )
            {
                return true;
            }
        }
        else {
            if ( empty( $this->value ) )
            {
                return true;
            }
            if ( in_array( $this->type, array('text', 'textarea') ) && trim($this->value) == '' )
            {
                return true;
            }
            if ( in_array( $this->type, array('int', 'float') ) && $this->value == 0 )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверит значение поля на соответствие типу, а также заполнено ли
     * значение обязательного поля
     */
    function validate()
    {
        $classes    = explode( ' ', trim($this->class) );
        foreach ( $classes as $i => $class ) {
            if ( $class == 'error' ) {
                unset( $classes[ $i ] );
            }
        }

        // по умолчанию валидно
        $error  = 0;

        // не валидно
        if ( $this->isRequired() && $this->isEmpty() )
        {
            //    или если его значение пустое
            $this->form->addFeedback("&laquo;{$this->label}&raquo; нужно заполнить");
            $error = 2;
        }
            //    если значение не соответствует типу
        elseif ( ! ( $this->checkValue( $this->value ) || $this->isEmpty() ) )
        {
            $this->form->addFeedback("&laquo;{$this->label}&raquo; не соответствует типу");
            $error = 1;
        }

        if ( $error > 0 ) {
            $classes[] = 'error';
            $this->class    = join(' ', $classes);
        }

        return ! $error;
    }

    /**
     * Возвращает HTML для поля
     * @return string
     */
    function html()
    {
        // если поле скрытое, то вывести скрытое
        if ( $this->hidden ) {
            return $this->doInputHidden();
        }

        $field = array();

        $field['id']  = "id='{$this->getId()}'";

        $field['type']     = "type='{$this->type}'";

        $class = explode( ' ', $this->class );

        // исключения из типов
        if ( in_array( $this->type, array('int', 'float', 'date', 'password') ) )
        {
            $class['type'] = 'text';
        }

        if ( $this->isRequired() ) {
            $class['required']  = "required";
        }

        $field['class']    = $class;

        $field['name']     = "name='{$this->form->name()}[{$this->name}]'";
        $field['value']    = "value='{$this->getStringValue()}'";

        if ( $this->readonly ) {
            $field['readonly'] = 'readonly="readonly"';
            $field['class']['readonly'] = 'readonly';
        }

        if ( $this->disabled ) {
            $field['disabled'] = 'disabled="disabled"';
        }

        return $this->htmlTpl( $this->doInput( $field ) );
    }

    /**
     * Декоратор полей
     * @param $html
     */
    function htmlTpl( $html )
    {
        $label_class = '';
        if ( strpos( $this->class, 'error' ) !== false ) {
            $label_class = 'class="error"';
        }
        return "<div class='b-form-field'>
            <label for='{$this->getId()}' {$label_class}>{$this->label}".($this->isRequired()?' <b>*</b> ':'')."</label>
            <div class='b-form-field-{$this->type}'>
                {$html}
            </div>
        </div>";
    }

    /**
     * Вернет HTML для поля
     * @return string
     */
    function doInput( &$field )
    {
        $field['class']    = 'class="'.join(' ', $field['class']).'"';
        return "<input ".join(' ', $field)." autocomplete='off' />";
    }

    /**
     * Вернет HTML для скрытого поля
     * @return string
     */
    function doInputHidden()
    {
        return "<input type='hidden' name='{$this->form->name()}[{$this->name}]' id='{$this->getId()}' value='{$this->value}' />";
    }




}