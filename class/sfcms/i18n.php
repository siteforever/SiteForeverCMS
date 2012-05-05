<?php
/**
 * Переводчик
 */

class Sfcms_i18n
{

    private $_lang;
    private $_dictionary;

    /**
     * @var Sfcms_i18n
     */
    private static $_instance;

    protected $_table = array(
        'кон'=> 'con',
        'ком'=> 'com',
        'кат'=> 'cat',
        'а'  => 'a',
        'б'  => 'b',
        'в'  => 'v',
        'г'  => 'g',
        'д'  => 'd',
        'е'  => 'e',
        'ё'  => 'e',
        'ж'  => 'zh',
        'з'  => 'z',
        'и'  => 'i',
        'й'  => 'i',
        'к'  => 'k',
        'л'  => 'l',
        'м'  => 'm',
        'н'  => 'n',
        'о'  => 'o',
        'п'  => 'p',
        'р'  => 'r',
        'с'  => 's',
        'т'  => 't',
        'у'  => 'u',
        'ф'  => 'f',
        'х'  => 'h',
        'ц'  => 'c',
        'ч'  => 'ch',
        'ш'  => 'sh',
        'щ'  => 'sch',
        'ъ'  => 'j',
        'ы'  => 'y',
        'ь'  => 'j',
        'э'  => 'e',
        'ю'  => 'yu',
        'я'  => 'ya',
        ' '  => '_',
    );

    protected function __construct()
    {
    }

    public function setLanguage( $lang = 'en' )
    {
        $this->_lang = $lang;
        $dict_file   = SF_PATH  . DIRECTORY_SEPARATOR . 'protected'
                                . DIRECTORY_SEPARATOR . 'lang'
                                . DIRECTORY_SEPARATOR . $this->_lang . '.php';
        if( ! file_exists( $dict_file ) ) {
            throw new Exception( 'Dictionary for language ' . $this->_lang . ' not found in file ' . $dict_file );
        }
        $this->_dictionary = @include( $dict_file );
    }

    /**
     * @static
     * @return Sfcms_i18n
     */
    public static function getInstance()
    {
        if( is_null( self::$_instance ) ) {
            // Locale
            setlocale( LC_ALL, 'en_US.UTF-8', 'en_US', 'English', 'C' );
            setlocale( LC_TIME, 'rus', 'ru_RU.UTF-8', 'Russia' );
            self::$_instance = new Sfcms_i18n();
        }
        return self::$_instance;
    }

    /**
     * @param $text
     * @return string
     */
    public function write( $text )
    {
        if( isset( $this->_dictionary[ $text ] ) ) {
            return $this->_dictionary[ $text ];
        }
        return $text;
    }

    /**
     * Транслитерация
     * @param string $str
     * @return string
     */
    public function translit( $str )
    {
        foreach( $this->_table as $rus => $eng ) {
            $str    = preg_replace('/'.$rus.'/ui', $eng, $str); // For uft8 support
        }
        $str    = preg_replace('/[^a-z0-9]+/i', '-', $str);
        $str    = trim( $str, '-' );
        return $str;
    }
}
