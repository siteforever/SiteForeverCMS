<?php
/**
 * Объект запроса
 */
class Request
{
    const TEXT  = FILTER_SANITIZE_STRING;
    const INT   = FILTER_SANITIZE_NUMBER_INT;
    const FLOAT = FILTER_SANITIZE_NUMBER_FLOAT;
    const URL   = FILTER_SANITIZE_URL;
    const EMAIL = FILTER_SANITIZE_EMAIL;
    const IP    = FILTER_VALIDATE_IP;

    const TYPE_ANY  = '*/*';
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';

    private $feedback = array();

    private $request = array();

    private $_assets = null;

    private $ajax = null;
    private $ajax_type = self::TYPE_ANY;

    private $error = 0;

    private $response = array(
        'error' => '',
        'errno' => 0,
    );

    /**
     * Созание запроса
     */
    public function __construct()
    {
        $this->_assets = new Siteforever_Assets();

        if ( isset( $_REQUEST[ 'route' ] ) ) {
            $_REQUEST[ 'route' ] = preg_replace( '/\?.*/', '', $_REQUEST[ 'route' ] );
        }

        if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
            $q_pos = strrpos( $_SERVER[ 'REQUEST_URI' ], '?' );
            $req   = trim( substr( $_SERVER[ 'REQUEST_URI' ], $q_pos + 1, strlen( $_SERVER[ 'REQUEST_URI' ] ) ), '?&' );
        }

        if ( isset( $_SERVER[ 'argv' ] ) ) {
            foreach ( $_SERVER[ 'argv' ] as $arg ) {
                if ( strpos( $arg, '=' ) ) {
                    list( $arg_key, $arg_val ) = explode( '=', $arg );
                    $this->set( $arg_key, $arg_val );
                }
            }
        }

        // дополняем массив $_REQUEST не учтенными значениями
        if ( isset( $req ) && $opt_req = explode( '&', $req ) ) {
            foreach ( $opt_req as $opt_req_item ) {
                $opt_req_item = explode( '=', $opt_req_item );
                if ( !isset( $_REQUEST[ $opt_req_item[ 0 ] ] ) && isset( $opt_req_item[ 1 ] ) ) {
                    $_REQUEST[ $opt_req_item[ 0 ] ] = $opt_req_item[ 1 ];
                }
            }
        }

        foreach ( $_REQUEST as $key => $val ) {
            if ( is_array( $val ) ) {
                $this->request[ $key ] = $val;
            }
            else {
                $this->request[ $key ] = addslashes( $val );
            }
        }

        if ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) {

            $this->ajax = true;

            if ( isset( $_SERVER[ 'HTTP_ACCEPT' ] ) ) {

                if ( stripos( $_SERVER[ 'HTTP_ACCEPT' ], 'application/json' ) !== false ) {
                    $this->ajax_type = self::TYPE_JSON;
                }
                elseif ( stripos( $_SERVER[ 'HTTP_ACCEPT' ], 'application/xml' ) !== false ) {
                    $this->ajax_type = self::TYPE_XML;
                }
                else {
                    $this->ajax_type = self::TYPE_ANY;
                }
            }
        }

        $theme = App::getInstance()->getConfig()->get( 'template.theme' );

        $this->request[ 'path' ] = $this->request[ 'tpldata' ][ 'path' ] = array(
            //'misc'      => '/misc',
            'css'       => '/themes/' . $theme . '/css',
            'js'        => '/themes/' . $theme . '/js',
            'images'    => '/themes/' . $theme . '/images',
            'misc'      => '/misc',
        );

        $this->request[ 'resource' ] = 'theme:';
        $this->request[ 'template' ] = 'index';

        $this->addStyle( $this->request[ 'path' ][ 'misc' ] . '/reset.css' );
//        $this->addStyle( $this->request[ 'path' ][ 'misc' ] . '/jquery/lightbox/css/jquery.lightbox-0.5.css' );
        $this->addStyle( $this->request[ 'path' ][ 'misc' ] . '/jquery/fancybox/jquery.fancybox-1.3.1.css' );
        $this->addStyle( $this->request[ 'path' ][ 'misc' ] . '/siteforever.css' );

        $this->addScript( $this->request[ 'path' ][ 'misc' ] . '/jquery/jquery-1.7.2'.(App::$DEBUG?'':'.min').'.js' );
//        $this->addScript( $this->request[ 'path' ][ 'misc' ] . '/jquery/lightbox/jquery.lightbox-0.5.js' );
        $this->addScript( $this->request[ 'path' ][ 'misc' ] . '/jquery/fancybox/jquery.fancybox-1.3.1.js' );
        $this->addScript( $this->request[ 'path' ][ 'misc' ] . '/siteforever.js' );
    }


    /**
     * @return string
     */
    public function getController()
    {
        return $this->get( 'controller' );
    }


    /**
     * @return string
     */
    public function getAction()
    {
        return $this->get( 'action' );
    }


    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function getAjax()
    {
        if ( null === $this->ajax ) {
            if ( isset( $_SERVER[ 'X-Requested-With' ] ) && 'XMLHttpRequest' !== $_SERVER[ 'X-Requested-With' ] ) {
                $this->ajax = true;
            } else {
                $this->ajax = false;
            }
        }
        return $this->ajax;
    }

    /**
     * Установить обработку аякс принудительно
     * @param bool   $ajax
     * @param string $type
     *
     * @return void
     */
    public function setAjax( $ajax = false, $type = self::TYPE_ANY )
    {
        $this->ajax = $ajax;
        if ( $ajax )
            $this->ajax_type = $type;
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }

    /**
     * Тип запроса
     * @return string
     */
    public function getAjaxType()
    {
        return $this->ajax_type;
    }

    /**
     * Установить состояние ошибки
     * @param int $error
     *
     * @return void
     */
    public function setError( $error )
    {
        $this->error = $error;
    }

    /**
     * Вернуть состояние ошибки
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Получить список файлов стилей
     * @return array
     */
    public function getStyle()
    {
        return $this->_assets->getStyle();
    }

    /**
     * Добавить файл стилей
     * @param  $style
     *
     * @return void
     */
    public function addStyle( $style )
    {
        $this->_assets->addStyle( $style );
    }

    public function cleanStyle()
    {
        $this->_assets->cleanStyle();
    }

    public function getScript()
    {
        return $this->_assets->getScript();
    }

    public function addScript( $script )
    {
        $this->_assets->addScript( $script );
    }

    public function cleanScript()
    {
        $this->_assets->cleanScript();
    }

    /**
     * Установить значение
     * @param $key
     * @param $val
     *
     * @return void
     */
    public function set( $key, $val )
    {
        $path = explode( '.', $key );
        if ( count( $path ) == 1 ) {
            $this->request[ $key ] = $val;
        }
        else {
            $this->seti( $path, $val );
        }
    }

    /**
     * Получить значение
     * @param     $key
     * @param int $type
     * @param     $default
     *
     * @return mixed
     */
    public function get( $key, $type = FILTER_DEFAULT, $default = null )
    {
        $get  = '';
        $path = $key;
        if ( strpos( $key, '.' ) !== false ) {
            $path = explode( '.', $key );
        }
        if ( count( $path ) == 1 ) {
            if ( isset( $this->request[ $key ] ) ) {
                $get = $this->request[ $key ];
                if ( $type == FILTER_DEFAULT ) {
                    return $get;
                }
                if ( $type == self::INT && preg_match( '/[\+\-]?\d+/', $get ) ) {
                    return (int) $get;
                }
                if ( $type == self::FLOAT && preg_match( '/[\+\-]?\d+[\.,]?\d*/', $get ) ) {
                    return (float) $get;
                }
            }
        }
        else {
            $get = $this->geti( $path );
        }
        if ( is_array( $get ) ) {
            return $get;
        }

        return filter_var( $get, $type ) ? filter_var( $get, $type ) : $default;
    }

    /**
     * Получить значение по алиасу
     * @param array $path
     * @return mixed
     */
    protected function geti( array $path )
    {
        $data = $this->request;
        foreach ( $path as $part ) {
            if ( isset( $data[ $part ] ) ) {
                $data = $data[ $part ];
            }
            else {
                return null;
            }
        }
        return $data;
    }

    /**
     * Установить свое значение по алиасу
     * @param array $path
     * @param $value
     */
    protected function seti( array $path, $value )
    {
        $data =& $this->request;

        foreach ( $path as $part ) {
            if ( !isset( $data[ $part ] ) ) {
                $data[ $part ] = array();
            }
            $data =& $data[ $part ];
        }
        $data = $value;
    }

    /**
     * Установить заголовок страницы
     * @param String $text
     *
     * @return void
     */
    public function setContent( $text )
    {
        $this->request[ 'tpldata' ][ 'page' ][ 'content' ] = $text;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getContent()
    {
        if ( isset( $this->request[ 'tpldata' ][ 'page' ][ 'content' ] ) ) {
            return $this->request[ 'tpldata' ][ 'page' ][ 'content' ];
        }
        return '';
    }


    /**
     * Установить контент страницы
     * @param String $text
     *
     * @return void
     */
    public function setTitle( $text )
    {
        $this->request[ 'tpldata' ][ 'page' ][ 'title' ] = $text;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        if ( isset( $this->request[ 'tpldata' ][ 'page' ][ 'title' ] ) ) {
            return $this->request[ 'tpldata' ][ 'page' ][ 'title' ];
        }
        return '';
    }

    /**
     * Установит имя шаблона для вида
     * @param  $tpl
     *
     * @return void
     */
    public function setTemplate( $tpl )
    {
        $this->request[ 'template' ] = $tpl;
    }

    /**
     * Вернет имя текущего шаблона для вида
     * @return array|string
     */
    public function getTemplate()
    {
        return $this->request[ 'template' ];
    }

    /**
     * Добавить сообщение
     * @param $msg
     *
     * @return void
     */
    public function addFeedback( $msg )
    {
        if ( is_string( $msg ) ) {
            $this->feedback[ ] = $msg;
            return;
        }
        if ( is_array( $msg ) ) {
            foreach ( $msg as $m ) {
                if ( is_string( $m ) ) {
                    $this->feedback[ ] = $m;
                }
            }
        }
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function getFeedbackString( $sep = "<br />\n" )
    {
        $ret = '';
        if ( count( $this->feedback ) ) {
            /*$ret = '<div class="b-request-feedback">'.
                    join( $sep, $this->feedback ).
                    '</div>';*/
            $ret = join( $sep, $this->feedback );
        }
        return $ret;
    }

    /**
     * Добавить параметр в ответ
     * @param  $key
     * @param  $value
     *
     * @return void
     */
    public function setResponse( $key, $value )
    {
        $this->response[ $key ] = $value;
    }

    /**
     * Установить код ошибки
     * @param int    $errno
     * @param string $error
     *
     * @return void
     */
    public function setResponseError( $errno, $error = '' )
    {
        if ( !$errno && !$error ) {
            $error = t( 'No errors' );
        }
        $this->setResponse( 'errno', $errno );
        $this->setResponse( 'error', $error );
    }

    /**
     * Вернет респонс массивом
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Вернет ответ как Json
     * @return string
     */
    public function getResponseAsJson()
    {
        return json_encode( $this->response );
    }

    /**
     * Вернет ответ как XML
     * @return mixed
     */
    public function getResponseAsXML()
    {
        $xml = new SimpleXMLElement( '<response></response>' );

        array_walk_recursive( $this->response, array( self, 'arrayWalkToXML' ), $xml );

        return $xml->asXML();
    }

    /**
     * Функция коллбэк
     * @param                  $item
     * @param                  $key
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    public function arrayWalkToXML( $item, $key, SimpleXMLElement $xml )
    {
        $xml->addChild( $key, $item );
    }

    public function debug()
    {
        printVar( $this->request );
    }

    /**
     * Очистит все параметры запроса
     */
    public function clearAll()
    {
        $this->request = array();
    }
}