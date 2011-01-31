<?php
spl_autoload_register(array('Loader', 'load'));

/**
 * Загрузчик классов
 */
class Loader
{
    static $autoload_count = 0;

    static function load( $class_name )
    {
        $class_name = strtolower( $class_name );

        if ( in_array( $class_name, array('finfo') ) ) {
            return false;
        }

        if ( $class_name == 'register' ) {
            throw new Exception('Autoload Register class');
        }

        // PEAR format autoload
        $file = str_replace( '_', DS, $class_name ).'.php';

        if ( @include_once $file ) {
            if ( DEBUG_AUTOLOAD ) {
                self::$autoload_count++;
                FirePHP::getInstance(true)->log($file, 'include '.self::$autoload_count);
            }
            return true;
        }
        return false;
        /*if ( ! class_exists( $class_name ) ) {
            throw new Exception("Class $class_name not found in file $file");
        }*/
    }
}
