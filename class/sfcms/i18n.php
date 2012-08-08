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
        $dictFile   = SF_PATH  . DIRECTORY_SEPARATOR . 'protected'
                                . DIRECTORY_SEPARATOR . 'lang'
                                . DIRECTORY_SEPARATOR . $this->_lang . '.php';
        if( ! file_exists( $dictFile ) ) {
            throw new Exception( 'Dictionary for language ' . $this->_lang . ' not found in file ' . $dictFile );
        }
        $this->_dictionary = @include( $dictFile );

        // Prepare dictionary for JS
        $jsDictFile = ROOT.DIRECTORY_SEPARATOR.'_runtime'.DIRECTORY_SEPARATOR.'i18n.'.$this->_lang.'.js';
        $jsI18nFile = SF_PATH.DIRECTORY_SEPARATOR.'misc'.DIRECTORY_SEPARATOR.'siteforever'.DIRECTORY_SEPARATOR.'i18n.js';

        if ( App::isDebug() ) {
            unlink( $jsDictFile );
        }

        clearstatcache();
        if ( ! file_exists( $jsDictFile )
            || filemtime( $dictFile ) < filemtime( $jsDictFile )
            || filemtime( $jsI18nFile ) < filemtime( $jsDictFile ) )
        {
            $jsDict = array('// RUNTIME DICTIONARY FILE');
            $jsDict[] = file_get_contents( $jsI18nFile );

            $dictList = glob( dirname( $dictFile ) . DIRECTORY_SEPARATOR . $this->_lang . DIRECTORY_SEPARATOR . '*.php' );
            foreach( $dictList as $file ) {
                $this->_dictionary[ 'cat_' . basename( $file, '.php' )] = @include( $file );
            }
            $jsDict[] = "siteforever.i18n._dict = ".json_encode( $this->_dictionary ).';';
            file_put_contents( $jsDictFile, join("\n\n", $jsDict) );
        }
        App::getInstance()->addScript( '/_runtime/i18n.'.$this->_lang.'.js' );
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
     * Return translated message
     * @param string $message
     * @return string
     */
    public function write( $message )
    {
        switch ( func_num_args() ) {
            case 1:
                if( isset( $this->_dictionary[ $message ] ) ) {
                    return $this->_dictionary[ $message ];
                }
                break;
            case 2:
                if ( is_string( func_get_arg(1) ) ) {
                    return $this->getCategoryTranslate( func_get_arg(0), func_get_arg(1) );
                }
                if ( is_array( func_get_arg(1) ) ) {
                    return $this->getCategoryTranslate( null, func_get_arg(0), func_get_arg(1) );
                }
                break;
            case 3:
                return $this->getCategoryTranslate( func_get_arg(0), func_get_arg(1), func_get_arg(2) );
        }

        return $message;
    }

    /**
     * @param $category
     * @param $message
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    protected function getCategoryTranslate( $category, $message, $params = array() )
    {
        $category = strtolower( $category );
        if ( $category && ! isset( $this->_dictionary[ $category ] ) ) {
            $dictFile   = SF_PATH  . DIRECTORY_SEPARATOR . 'protected'
                                    . DIRECTORY_SEPARATOR . 'lang'
                                    . DIRECTORY_SEPARATOR . $this->_lang
                                    . DIRECTORY_SEPARATOR . $category . '.php';
            if( ! file_exists( $dictFile ) ) {
                throw new Exception( 'Dictionary ' . $category . ' for language ' . $this->_lang
                    . ' not found in file ' . $dictFile );
            }
            $this->_dictionary[ 'cat_' . $category ] = @include( $dictFile );
        }
        if ( null !== $category && isset( $this->_dictionary[ 'cat_' . $category ][ $message ] ) ) {
            $message = $this->_dictionary[ 'cat_' . $category ][ $message ];
        } elseif ( isset( $this->_dictionary[ $message ] ) ) {
            $message = $this->_dictionary[ $message ];
        }
        foreach ( $params as $key => $val ) {
            $message = str_replace( $key, $val, $message );
        }
        return $message;
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
