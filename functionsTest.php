<?php

/**
 * Перенаправление на другой урл
 * @param string $url
 * @param array $params
 * @return void
 */
function redirect( $url = '', $params = array() )
{
    print __FUNCTION__;
}

/**
 * Перезагрузить страницу на нужную
 * @param string $url
 * @param array $params
 * @return void
 */
function reload( $url = '', $params = array() )
{
    print __FUNCTION__;
}

/**
 * Печать дампа переменной
 * @param $var
 */
function printVar( $var )
{
    print __FUNCTION__;
}

/**
 * Создаст ссылку
 * @deprecated
 * @param string $url
 * @param array  $params
 */
function href( $url, $params = array() )
{
    print __FUNCTION__;
}


/**
 * Вернет HTML код для иконки
 * @param string $name
 * @param string $title
 * @return string
 */
function icon( $name, $title='' )
{
    print __FUNCTION__;
}

/**
 * Проверяет условие
 * @param $cond
 * @param $msg
 */
function ensure( $cond, $msg )
{
    print __FUNCTION__;
}

/**
 * Отправить сообщение
 * @param string $from
 * @param string $to
 * @param string $subject
 * @param string $message
 */
function sendmail( $from, $to, $subject, $message )
{
    print __FUNCTION__;
}

/**
 * Напечатать переведенный текст
 * @param string $text
 * @return void
 */
function t($text)
{
    print __FUNCTION__;
}

/**
 * Транслитерация
 * @param string $str
 * @return string
 */
function translit( $str )
{
    print __FUNCTION__;
}





/**
 * Создает миниатюру картинки из файла с именем $newfile в файл $thumbfile
 *
 * @param string $newfile
 * @param string $thumbfile
 * @return bool
 */
function createThumb( $srcfile, $thumbfile, $thumb_w, $thumb_h, $method )
{
    print __FUNCTION__;
}