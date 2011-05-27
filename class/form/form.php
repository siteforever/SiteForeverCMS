<?php
/**
 * Класс формы
 * @author keltanas
 */
class Form_Form implements ArrayAccess
{
    /*
     * 1. Получить значения из POST
     * 2. Обработать значения по шаблону, соответственно типам
     * 3. Проверить заполнение обязательных полей
     * 4. Выводить форму в шаблоны
     * 5. Динамически управлять типами и видимостью полей
     * 6. Возвращать данные запроса в виде проверенного массива для создания Domain классов
     */

    const DEBUG = false;

    private
        $_name,
        $_method,
        $_action,
        $_class,
        /**
         * Массив объектов полей формы
         */
        $_fields,
        /**
         * Массив кнопок
         */
        $_buttons,
        /**
         * Данные, полученные из _POST или _GET
         */
        $_data;

    private $_err_required  = 0;
    private $_err_untype    = 0;

    private $_feedback      = array();

    /**
     * Создает форму согласно конфигу
     * @param $config
     */
    function __construct( $config, Request $request = null )
    {
        if ( isset( $request )  ) {
            $request->addScript( $request->get('path.misc').'/jquery.form.js' );
            //$request->addScript( $request->get('path.misc').'/jquery.blockUI.js' );
            //$request->addScript( $request->get('path.misc').'/forms.js' );
        }


        if ( ! isset( $config['name'] ) ) {
            throw new Exception('Для формы нужно определить обязательный параметр name');
        }
        if ( ! isset( $config['fields'] ) ) {
            throw new Exception('Для формы нужно определить массив полей fields');
        }
        $this->_name    = $config['name'];
        $this->_method  = isset( $config['method'] ) ? $config['method'] : 'post';
        $this->_action  = isset( $config['action'] ) ? $config['action'] : '';
        $this->_class   = isset( $config['class'] ) ? $config['class'] : 'standart ajax';

        foreach( $config['fields'] as $fname => $field )
        {
            // Обработка разделителей
            if ( in_array( $fname, array('separate', 'separator', 'sep') ) ||
                     in_array( $field, array('separate', 'separator', 'sep') )
            ) {
                $this->_fields[] = "<hr class='separator' />";
                continue;
            }

            // Обработка HTML
            if ( is_string( $field ) ) {
                $this->_fields[] = $field;
                continue;
            }

            $hidden = false;

            $field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

            // тип hidden преобразовать в скрытый text
            if ( $field['type'] == 'hidden' /*|| in_array( 'hidden', $field )*/ ) {
                self::DEBUG ? print 'hidden' : false;
                $hidden = true;
                $field['type']  = 'text';
            }

            // физический класс обработки поля
            $field_class = 'Form_Field_'.$field['type'];

            if ( self::DEBUG ) {
                print '('.$field_class.($hidden?', hidden':'').') '.$fname.'<br />';
            }

            // экземпляр поля
            if ( ! class_exists( $field_class ) ) {
                throw new Exception( 'Class not found '.$field_class );
            }

            $obj_field   = new $field_class( $this, $fname, $field );

            // идентификатор поля
            $field_id    = $obj_field->getId();

            if ( in_array( $field['type'], array('submit','reset','button') ) ) {
                $this->_buttons[ $field_id ] = $obj_field;
                continue;
            }

            $this->_fields[ $field_id ] = $obj_field;

            if ( $hidden )
                $this->_fields[ $field_id ]->hide();
        }
    }

    /**
     * @return form_Uploader
     */
    function uploader()
    {
        if ( !isset( $this->uploader ) )
        {
            $this->uploader = new form_uploader( $this );
        }
        return $this->uploader;
    }

    /**
     * Очищает значения полей формы
     */
    function clear()
    {
        foreach( $this->_fields as $field )
        {
            if ( is_object( $field ) )
                $field->clear();
        }
    }

    /**
     * Вернет значение поля формы по имени
     * @return mixed
     */
    function __get( $key )
    {
        return $this->getField( $key );
    }

    /**
     * Установит значение полю
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set( $key, $value )
    {
        $this->getField( $key )->setValue( $value );
    }

    /**
     * Вернет поле формы по имени
     * @return form_Field
     */
    function getField( $key )
    {
        $id = $this->_name.'_'.$key;
        if ( isset( $this->_fields[$id] ) )
        {
            return $this->_fields[$id];
        }
        throw new Form_Exception("Field '{$key}' not found");
    }

    /**
     * Дернет из запроса значения полей
     */
    function getPost()
    {
        if ( $this->isSent() )
        {
            $data = $this->_data;

            /**
             * @var $field form_Field
             */
            foreach ( $this->_fields as $field )
            {
                if ( is_object( $field ) ) {
                    if ( isset( $data[ $field->getName() ] ) )
                    {
                        $field->setValue( $data[ $field->getName() ] );
                    }
                    if ( $field->getType() == 'file' ) {
                        $field->setValue('');
                    }
                }
            }
            //reg::set('ajax', true);
            return true;
        }
        return false;
    }

    /**
     * Отправлена ли форма?
     */
    function isSent()
    {
        if ( isset( $_REQUEST[ $this->_name ] ) ) {
            $this->_data = $_REQUEST[ $this->_name ];
            return true;
        }
        return false;
    }

    /**
     * Установит или вернет значение имени формы
     * @param string $name
     * @return string
     */
    function name( $name = '' )
    {
            if ( $name ) {
                $this->_name = $name;
            }
            else {
                return $this->_name;
            }
    }


    /**
     * html - код формы
     */
    function html( $hint = true, $buttons = true )
    {
        $html     = array();

        $html[]   = "<form name='form_{$this->_name}' id='form_{$this->_name}' ".
                    "class='{$this->_class}' method='{$this->_method}' action='{$this->_action}' ".
                    "enctype='multipart/form-data'>";

        /*$feedback   = $this->getFeedbackString();
        if ( trim($feedback) ) {
            $html[] = "<p class='error'>{$feedback}</p>";
        }*/

        foreach ( $this->_fields as $field ) {
            if ( is_object( $field ) )
                $html[] = $field->html();
            elseif ( is_string( $field ) )
                $html[] = $field;
        }

        if ( $buttons && is_array( $this->_buttons ) ) {
            //$html[] = '<hr />';
            foreach ( $this->_buttons as $button ) {
                $html[]  = $button->html();
            }
        }

        if ( $hint ) {
            $html[]   = "<p><b>*</b> - поля, отмеченные звездочкой обязательны для заполнения</p>";
        }
        $html[]   = "</form>";

        return join("\n", $html);
    }

    /**
     * Вернет массив значений
     * Как правило, нужно для использования с базой данных
     * @return array
     */
    function getData( $toString = false )
    {
        $data = array();
        foreach( $this->_fields as $field )
        {
            if ( is_object( $field ) ) {
                if ( ! in_array( $field->getType(), array('submit', 'separator', 'captcha') ) )
                {
                    if ( $toString ) {
                        $value = $field->getStringValue();
                    } else {
                        $value = $field->getValue();
                        if ( is_array( $value ) && count( $value ) == 1 ) {
                            $value = join( '', $value );
                        }
                    }

                    $data[ $field->getName() ] = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Установит массив значений
     * Как правило, нужно для использования с базой данных
     * @return array
     */
    function setData( $data )
    {
        if ( count($this->_fields) == 0 ) {
            throw new Exception( 'Форма не содержит полей' );
            return false;
        }
        foreach( $this->_fields as $field )
        {
            if ( is_object( $field ) && ! in_array( $field->getType(), array('submit', 'separator') ) )
            {
                if ( isset($data[ $field->getName() ]) )
                {
                    $value = $data[ $field->getName() ];

                    if ( ! $field->setValue( $value ) ) {
                        //$this->addFeedback("Значение поля {$field->getName()} не установлено");
                    };
                }
                else {
                    //$this->addFeedback("Значение поля {$field->getName()} не найдено");
                }
            }
        }
        return true;
    }

    /**
     * Проверка валидности формы
     * @return bool
     */
    function validate()
    {
        $valid = true;
        foreach( $this->_fields as $field ) {
            if( is_object( $field ) ) {
                $ret = $field->validate();
                $valid &= ($ret == 1) ? true : false;
                switch ( $ret ) {
                    case -1:
                        $this->_err_untype++;
                        break;
                    case -2:
                        $this->_err_required++;
                        break;
                }
            }
        }
        return $valid;
    }



    /**
     * Добавит сообщение обратной связи
     * @param  $msg
     * @return void
     */
    function addFeedback( $msg )
    {
        array_push( $this->_feedback, $msg );
    }

    /**
     * Вернет сообщения обратной связи как массив
     * @return array
     */
    function getFeedback()
    {
        return $this->_feedback;
    }

    /**
     * Вернет сообщения обратной связи как строку
     * @param string $sep
     * @return string
     */
    function getFeedbackString( $sep = "<br />\n" )
    {
        return join( $sep, $this->_feedback );
    }


    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * The offset to assign the value to.
     * @param mixed $value
     * The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getField( $offset )->setValue( $value );
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getField( $offset )->getValue();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * An offset to check for.
     * @return boolean Returns true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->getField( $offset ) ? true : false;
    }
}