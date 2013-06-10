<?php
use Sfcms\i18n;

/**
 * Печать дампа переменной
 * @param $var
 */
function printVar( $var )
{
    print '<pre>'.print_r( $var, 1 ).'</pre>';
}

/**
 * Отправить сообщение
 * @param string $from
 * @param string $to
 * @param string $subject
 * @param string $msg
 * @param string $mime_type
 */
function sendmail($from, $to, $subject, $msg, $mime_type = 'plain/text')
{
    $mailer = new \Swift_Mailer(new \Swift_SendmailTransport());
    /** @var $message \Swift_Message */
    $message = $mailer->createMessage();
    $message
        ->setSubject($subject)
        ->setFrom($from)
        ->setTo($to)
        ->setBody($msg, $mime_type, 'utf-8');

    $mailer->send($message);
}

/**
 * Напечатать переведенный текст
 * @param string $cat
 * @param string $text
 * @param array $params
 * @return mixed
 */
function t( $cat, $text = '', $params = array() )
{
    return call_user_func_array(array(i18n::getInstance(),'write'), func_get_args());
}

/**
 * Заменяет в строке $replace подстроки $search на строку $subject
 * @param $subject
 * @param $replace
 * @param $search
 * @return string
 */
function str_random_replace( $subject, $replace, $search = '%h1%' )
{
    return str_replace( $search, $subject, trim( array_rand( array_flip( explode( "\n", $replace ) ) ) ) );
}

/**
 * Логирует состояние переменной в файл debug.txt
 * @param      $var
 * @param null $name
 *
 * @return bool|int
 */
function debugVar( $var, $name = null )
{
    static $first = 1;
    if ( isset( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] != '127.0.0.1' ) {
        return false;
    }
    //    if ( ! App::isDebug() ) {
    //        return false;
    //    }
    $trace = debug_backtrace();

    $aTrace = array();
    foreach ( $trace as $i => $t ) {
        if ( isset( $t['file'] ) ) {
            $aTrace[] = $i . ': ' . $t['file'] . ' line ' . $t['line'];
        } elseif ( isset( $t['class'] ) ) {
            $aTrace[] = $i . ': ' . $t['class'] . $t['type'] . $t['function'] . '('. implode( $t['args'] ) . ')';
        }
    }

    $content = ( $first ? "\n\n".str_pad('',20,'*')."\n\nDEBUG: "
        . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' )
        . '   ' . strftime('%Y-%m-%d %X') . "\n\n" : "" )
        . implode( "\n", array_slice( $aTrace, 0, 3 ) ) . "\n"
        . ( null !== $name ? $name . ' = ' : '' )
        . var_export( $var, 1 ) . "\n\n";

    $first = 0;
    return file_put_contents( ROOT.'/debug.log', $content, FILE_APPEND );
}





