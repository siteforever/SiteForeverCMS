<?php
/**
 * Класс для обработки ошибок.
 *
 * <strong>set_error_handler( array("error", "handler" ) ); // инициализация</strong>
 *
 * <ul>
 * <li>error::notice("Генерация пользовательской ошибки уровня E_USER_NOTICE")</li>
 * <li>error::warning("Генерация пользовательской ошибки уровня E_USER_WARNING")</li>
 * <li>error::fatal("Генерация пользовательской ошибки уровня E_USER_ERROR")</li>
 * </ul>
 *
 * @author  Ermin Nikolay
 */

class std_error
{

    /**
     * Строковые типы ошибок
     *
     * @var array
     */
    static $types = array(
        E_ERROR              => 'E_ERROR',
        E_WARNING            => 'E_WARNING',
        E_PARSE              => 'E_PARSE',
        E_NOTICE             => 'E_NOTICE',
        E_CORE_ERROR         => 'E_CORE_ERROR',
        E_CORE_WARNING       => 'E_CORE_WARNING',
        E_COMPILE_ERROR      => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING    => 'E_COMPILE_WARNING',
        E_USER_ERROR         => 'E_USER_ERROR',
        E_USER_WARNING       => 'E_USER_WARNING',
        E_USER_NOTICE        => 'E_USER_NOTICE',
        E_STRICT             => 'E_STRICT',
        E_RECOVERABLE_ERROR  => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED         => 'E_DEPRECATED',
        E_USER_DEPRECATED    => 'E_USER_DEPRECATED',
        E_ALL                => 'E_ALL',
    );

    /**
     * @var std_logger_logger
     */
    static $logger = null;


    static function init( std_logger_logger $logger = null )
    {
        self::$logger   = $logger;
        set_error_handler( array( self, 'handler' ) );
    }

    /**
     * Грубая ошибка
     *
     * @param $text
     * @return void
     */
    static function fatal( $text ) // чтобы был не конструктор
    {
        trigger_error( $text, E_USER_ERROR );
        die();
    }

    /**
     * Предупреджение
     *
     * @param $text
     * @return void
     */
    static function warning( $text )
    {
        trigger_error( $text, E_USER_WARNING );
    }

    /**
     * Примечание
     *
     * @param $text
     * @return void
     */
    static function notice( $text )
    {
        trigger_error( $text, E_USER_NOTICE );
    }

    /**
     * Обработчик ошибок
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param $errcontext
     * @return boolean
     */
    static function handler( $errno = 0, $errstr = '', $errfile = '', $errline = 0, $errcontext = array() )
    {
        $trace = self::backtrace();

        if ( false !== strpos( $errfile, 'smarty' ) ) {
            return true;
        }

        $address = isset( $_SERVER[ 'HTTP_HOST' ] ) && isset( $_SERVER['REQUEST_URI'] )
            ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\n\n"
            : '';

        $msg      =  $address
                    . self::$types[$errno] . " $errstr in $errfile:$errline\n\n"
                    .join("\n", $trace);

        if ( self::$logger ) {
            self::$logger->log( $msg, 'error' );
        }

        return true;
    }

    /**
     * Стек вызода
     *
     * @return string
     */
    static function backtrace()
    {
        if (!function_exists('debug_backtrace')) {
            return '';
        }

        $output = array();

        //$output    = "<ol>\n";
        $backtrace = debug_backtrace();

        foreach ($backtrace as $key_bt => $bt) {
            if ($key_bt == 0 || $key_bt == 1) {
                continue;
            }
            $args = '';
            if (count($bt['args'])) foreach ($bt['args'] as $a) {
                if (!empty($args)) {
                    $args .= ', ';
                }
                switch ( gettype($a) ) {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array('.count($a).')';
                    break;
                case 'object':
                    $args .= 'Object('.get_class($a).')';
                    break;
                case 'resource':
                    $args .= 'Resource('.strstr($a, '#').')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
                }
            }

            $bt['class']    = isset( $bt['class'] ) ? $bt['class'] : 'null';
            $bt['type']     = isset( $bt['type'] ) ? $bt['type'] : '.';
            $bt['file']     = isset( $bt['file'] ) ? $bt['file'] : 'no file';
            $bt['line']     = isset( $bt['line'] ) ? $bt['line'] : '0';

            if ( strpos($bt['file'], 'class.error.php') === false ) {
                $output[] = "file: {$bt['file']}:{$bt['line']} in {$bt['class']}{$bt['type']}{$bt['function']} ($args)";
            }
        }
        //$output .= "</ol>\n";

        return $output;
    }

}