<?php

/**
 * Перенаправление на другой урл
 * @param string $url
 * @param array $params
 * @return void
 */
function redirect( $url = '', $params = array() )
{
    Data_Watcher::instance()->performOperations();
	header("Location: ".App::getInstance()->getRouter()->createLink( $url, $params ));
	die();
}

/**
 * Перезагрузить страницу на нужную
 * @param string $url
 * @param array $params
 * @return void
 */
function reload( $url = '', $params = array() )
{
    Data_Watcher::instance()->performOperations();
	die('<script type="text/javascript">window.location.href = "'.
        App::getInstance()->getRouter()->createLink( $url, $params ).'";</script>');
}

/**
 * Печать дампа переменной
 * @param $var
 */
function printVar( $var )
{
	Error::dump( $var );
}

/**
 * Создаст ссылку
 * @deprecated
 * @param string $url
 * @param array  $params
 */
function href( $url, $params = array() )
{
    return 'href="'.App::getInstance()->getRouter()->createLink( $url, $params ).'"';
}


/**
 * Вернет HTML код для иконки
 * @param string $name
 * @param string $title
 * @return string
 */
function icon( $name, $title='' )
{
    $title = $title ? $title : $name;
    return "<img title='{$title}' alt='{$title}' src='/images/admin/icons/{$name}.png' />";
}

/**
 * Проверяет условие
 * @param $cond
 * @param $msg
 */
function ensure( $cond, $msg )
{
    if ( !$cond ) {
        print $msg;
    }
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
    $header="Content-type: text/plain; charset=\"UTF-8\"\n";
    $header.="From: {$from}\n";
    $header.="Subject: $subject\n";
    $header.="Content-type: text/plain; charset=\"UTF-8\"\n";

    mail($to, $subject, $message, $header);
}

/**
 * Напечатать переведенный текст
 * @param string $text
 * @return void
 */
function t($text)
{
    return translate::getInstance()->write($text);
}

/**
 * Транслитерация
 * @param string $str
 * @return string
 */
function translit( $str )
{
    $table = array(
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'e',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'i',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ъ' => 'j',
        'ы' => 'y',
        'ь' => 'j',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        ' ' => '_',
    );
    $strlen = strlen( $str );
    foreach ( $table as $rus => $eng ) {
        $str = str_replace( $rus, $eng, $str );
    }
    return $str;
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
    /*
    * Создание миниатюр
    */
    // размеры исходного изображения
    $isize = getimagesize ( $srcfile );
    $iw = $isize [0];
    $ih = $isize [1];

    switch( $isize[2] ) {
        case IMAGETYPE_GIF:
            $im = imagecreatefromgif ( $srcfile );
            break;
        case IMAGETYPE_PNG:
            $im = imagecreatefrompng ( $srcfile );
            break;
        case IMAGETYPE_JPEG:
            $im = imageCreateFromJpeg ( $srcfile );
            break;
        default:
            return false;
    }

    // 1. пропорции
    $kh = $ih / $thumb_h;
    $kw = $iw / $thumb_w;
    // 2. выбираем коэффициент
    if ($method == 1) { // добавление полей
        $k = $kw < $kh ? $kh : $kw;
    } elseif ($method == 2) { // обрезание лишнего
        $k = $kw > $kh ? $kh : $kw;
    }
    // вычисляем размеры миниатюры
    $th = round( $ih / $k );
    $tw = round( $iw / $k );

    // 3. вычисляем координаты
    $m = array( // матрица распределения значений
        1 => array( // method 1
            'x' => array( 0 , -1 ),
            'y' => array( 1 , 0 ),
        ),
        2 => array( // method 2
            'x' => array( -0.5 , 0 ),
            'y' => array( 0 , 0.5 ),
        )
    );

    $otrez = round ( ($tw - $th) / 2 );
    $variant = $tw > $th ? 0 : 1;

    $ix = intval( $m[ $method ]['x'][ $variant ] * $otrez );
    $iy = intval( $m[ $method ]['y'][ $variant ] * $otrez );

    //printVar(array( $variant, $ix, $iy));

    // 4.
    $newim = imagecreatetruecolor ( $thumb_w, $thumb_h );
    //$bgcolor = imagecolorat($im, 0, 0);
    $bgcolor = imagecolorallocate($newim, 255, 255, 255);
    imagefill( $newim, 0, 0, $bgcolor ); // заливаем белым
    if ($newim && $im) {
        imagecopyresampled ( $newim, $im, $ix, $iy, 0, 0, $tw, $th, $iw, $ih );
    }

    $return = @imageJpeg ( $newim, $thumbfile, 80 );

    if ($im)
        imagedestroy ( $im );
    if ($newim)
        imagedestroy ( $newim );

    /*
     * Миниатюра создана
     */
    return $return;
}