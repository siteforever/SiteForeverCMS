<?php
class Request
{
    const TEXT      = FILTER_SANITIZE_STRING;
    const INT       = FILTER_VALIDATE_INT;
    const FLOAT     = FILTER_VALIDATE_FLOAT;
    const URL       = FILTER_VALIDATE_URL;
    const EMAIL     = FILTER_VALIDATE_EMAIL;
    const IP        = FILTER_VALIDATE_IP;

    const TYPE_ANY  = '*/*';
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';

    private $feedback   = array();
    private $request;

    private $_scripts   = array();
    private $_styles    = array();

    private $ajax       = false;
    private $ajax_type  = self::TYPE_ANY;

    private $error      = 0;



    function __construct()
    {
        if ( isset($_REQUEST['route']) ) {
            $_REQUEST['route'] = preg_replace('/\?.*/', '', $_REQUEST['route']);
        }

        $q_pos  = strrpos( $_SERVER['REQUEST_URI'], '?' );
        $req    = trim( substr( $_SERVER['REQUEST_URI'], $q_pos+1, strlen($_SERVER['REQUEST_URI']) ), '?&' );

        // дополняем массив $_REQUEST не учтенными значениями
        if ( $opt_req = explode('&', $req) ) {
            foreach( $opt_req as $opt_req_item ) {
                $opt_req_item = explode('=', $opt_req_item);
                if ( ! isset( $_REQUEST[ $opt_req_item[0] ] ) && isset($opt_req_item[1]) ) {
                    $_REQUEST[ $opt_req_item[0] ] = $opt_req_item[1];
                }
            }
        }

        foreach( $_REQUEST as $key => $val ) {
            if ( is_array( $val ) ) {
                $this->request[ $key ] = $val;
            } else {
                $this->request[ $key ] = addslashes( $val );
            }
        }

        if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {

            $this->ajax = true;

            if ( isset($_SERVER['HTTP_ACCEPT']) ) {

                if ( stripos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false ) {
                    $this->ajax_type    = self::TYPE_JSON;
                } elseif ( stripos( $_SERVER['HTTP_ACCEPT'], 'application/xml' ) !== false ) {
                    $this->ajax_type    = self::TYPE_XML;
                } else {
                    $this->ajax_type    = self::TYPE_ANY;
                }
            }
        }
    }



    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    function getAjax()
    {
        return $this->ajax;
    }

    /**
     * Тип запроса
     * @return string
     */
    function getAjaxType()
    {
        return $this->ajax_type;
    }

    /**
     * Установить состояние ошибки
     * @param int $error
     * @return void
     */
    function setError( $error )
    {
        $this->error    = $error;
    }

    /**
     * Вернуть состояние ошибки
     * @return int
     */
    function getError()
    {
        return $this->error;
    }

    /**
     * Получить список файлов стилей
     * @return array
     */
    function getStyle()
    {
        return $this->_styles;
    }

    /**
     * Добавить файл стилей
     * @param  $style
     * @return void
     */
    function addStyle( $style )
    {
        $this->_styles[] = $style;
    }

    function cleanStyle()
    {
        $this->_styles = array();
    }

    function getScript()
    {
        return $this->_scripts;
    }

    function addScript( $script )
    {
        $this->_scripts[] = $script;
    }

    function cleanScript()
    {
        $this->_scripts = array();
    }

    /**
     * Установить значение
     * @param $key
     * @param $val
     * @return void
     */
    function set( $key, $val )
    {
        $path = explode('.', $key);
        if ( count( $path ) == 1 ) {
            $this->request[ $key ] = $val;
        }
        else {
            $this->seti( $path, $val );
        }
    }

    /**
     * Получить значение
     * @param $key
     * @return mixed
     */
    function get( $key, $type = FILTER_DEFAULT )
    {
        $get = '';
        $path = $key;
        if ( strpos( $key, '.' ) !== false ) {
            $path = explode('.', $key);
        }
        if ( count( $path ) == 1 ) {
            if ( isset( $this->request[ $key ] ) ) {
                $get = $this->request[ $key ];
            }
        }
        else {
            $get = $this->geti( $path );
        }
        if ( is_array( $get ) ) {
            return $get;
        }
        //print $key.':'.$get.' => filtered: '.filter_var( $get, $type )."<br />\n";
        return filter_var( $get, $type );
    }

    /**
     * Получить значение по алиасу
     * @param $alias
     */
    protected function geti( $path )
    {
        $data = &$this->request;
        foreach( $path as $part ) {
            $data =& $data[ $part ];
        }
        return $data;
    }

    /**
     * Установить свое значение по алиасу
     * @param $alias
     * @param $value
     */
    protected function seti( $path, $value )
    {
        $data = &$this->request;
        foreach( $path as $part ) {
            $data =& $data[ $part ];
        }
        $data = $value;
    }

    /**
     * Установить заголовок страницы
     * @param String $text
     * @return void
     */
    function setContent( $text )
    {
        $this->request['tpldata']['page']['content']    = $text;
    }
    /**
     * Вернет заголовок страницы
     * @return string
     */
    function getContent()
    {
        if ( isset($this->request['tpldata']['page']['content']) ) {
            return $this->request['tpldata']['page']['content'];
        }
        return '';
    }


    /**
     * Установить контент страницы
     * @param String $text
     * @return void
     */
    function setTitle( $text )
    {
        $this->request['tpldata']['page']['title']    = $text;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    function getTitle()
    {
        if ( isset($this->request['tpldata']['page']['title']) ) {
            return $this->request['tpldata']['page']['title'];
        }
        return '';
    }

    /**
     * Добавить сообщение
     * @param $msg
     * @return void
     */
    function addFeedback( $msg )
    {
        $this->feedback[]   = $msg;
    }

    function getFeedback()
    {
        return $this->feedback;
    }

    function getFeedbackString( $sep = "<br />\n" )
    {
        $ret = '';
        if ( count($this->feedback) ) {
            /*$ret = '<div class="b-request-feedback">'.
                    join( $sep, $this->feedback ).
                    '</div>';*/
            $ret = join( $sep, $this->feedback );
        }
        return $ret;
    }

    function debug()
    {
        Error::dump( $this->request );
    }
}