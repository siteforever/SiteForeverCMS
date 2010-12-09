<?php

/**
 * Класс обработки ошибок и исключений
 * @author Nikolay Ermin
 */
class Error
{

    /**
     * Строковые типы ошибок
     *
     * @var array
     */
    static protected $types = array(
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        6143  => 'E_ALL',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
    );
    
    static protected $display_errors = 0;
    
    /**
     * Инициализация обработчиков
     * @return void
     */
    static function init()
    {
        self::$display_errors = App::$config->get('debug');
        set_error_handler(      array('Error', 'errorHandler' ) );
        set_exception_handler(  array('Error', 'exceptionHandler') );
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
    
    static function exception( $text )
    {
        throw new Exception( $text );
    }
    
    /**
     * Обработчик ошибок
     * @param int    $errno       код ошибки
     * @param string $errstr      текст ошибки
     * @param string $errfile     файл ошибки
     * @param int    $errline     строка ошибки
     * @param string $errcontext
     * @return void
     */
    static function errorHandler( $errno = 0, $errstr = '', $errfile = '', $errline = 0, $errcontext = array() )
    {
         if ( self::$display_errors ) {
            $trace = self::backtrace();
            print '<div class="error">'.$errstr." в ".$errfile.":".$errline."<br />\n<ol>\n";
            foreach ( $trace as $tr ) {
                print "<li>$tr</li>\n";
            }
            print '</ol></div>';
            return false;
        }
    }
    
    /**
     * Обработчик глобальных исключений
     * После него программа прерывается
     * @param $exception
     * @return void
     */
    static function exceptionHandler( Exception $exception )
    {
        print '<div class="error">Глобальное исключение: ' . $exception->getMessage() . '<br />';
        // вывести трассировку стека вызова
        foreach( $exception->getTrace() as $err ) {
            
            $err['file'] = isset( $err['file'] ) ? $err['file'] : '';
            $err['line'] = isset( $err['line'] ) ? $err['line'] : '';
            
            if ( strpos($err['file'], 'class.error.php') === false ) {
                $args = '';//@join(', ', $err['args']);
                if  ( $err['file'] ) {
                    $args = '<pre>'.htmlspecialchars( print_r( $err['args'], true ) ).'</pre>';
                }
                
                print "<div>Файл <i>{$err['file']}:{$err['line']}</i>".
                    " в методе <b>{$err['class']}::{$err['function']}</b>
                    ( $args )</div>";
            }
        }
        
        print '</div>';
    }
    
    /**
     * Стек вызода
     * @return array
     */
    static function backtrace()
    {
        if (!function_exists('debug_backtrace')) {
            return '';
        }

        $output = array();

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
                
                $type = gettype($a);
                
                switch ( $type )
                {
                    case 'integer':
                    case 'double':
                        $args .= $a;
                        break;
                    case 'string':
                        $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                        $args .= "String(&laquo;{$a}&raquo;)";
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
            
            if ( strpos($bt['file'], 'class.error.php') === false ) {
                $output[] = "Файл: {$bt['file']}:{$bt['line']}
                  в {$bt['class']}{$bt['type']}{$bt['function']} ($args)";
            }
        }

        return $output;
    }
    
    /**
     * Содержимое переменных
     * @param mixed $var
     * @return void
     */
    static function dump( $var )
    {
        $val = print_r($var, true);
        $val = preg_replace(array(
            '/array/xi',
            //'\([\w+\])xi',
        ), array(
            '<b>Array</b>',
            //'<font color="navy">$1</font>',
        ), $val);
        print '<pre style="text-align: left;">'.$val.'</pre>';
    }
        
}