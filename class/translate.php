<?php
/**
 * Переводчик
 */

class translate {

    private $lang;
    private $dictionary;

    private static $instance;

    protected function __construct()
    {
    }

    function setLanguage( $lang = 'en' )
    {
        $this->lang = $lang;
        $dict_file  = 'protected'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.$this->lang.'.php';
        if ( ! $this->dictionary = @include($dict_file) ) {
            throw new Exception('Dictionary for language '.$this->lang.' not found in file '.$dict_file);
        }
    }

    /**
     * @static
     * @return translate
     */
    static function getInstance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function write( $text )
    {
        if ( isset( $this->dictionary[$text] ) ) {
            return $this->dictionary[$text];
        }
        return $text;
    }
}
